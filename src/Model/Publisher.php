<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Tracedock\TransactionTracking\Api\ConfigInterface;
use Tracedock\TransactionTracking\Api\PublisherInterface as TracedockPublisherInterface;

class Publisher implements TracedockPublisherInterface
{
    private const TOPIC_NAME = 'tracedock_tracking';

    private PublisherInterface $publisher;

    private LoggerInterface $logger;

    private ConfigInterface $config;

    private Mapper $mapper;

    private SerializerInterface $serializer;

    public function __construct(
        PublisherInterface $publisher,
        Mapper $mapper,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        ConfigInterface $config
    ) {
        $this->publisher  = $publisher;
        $this->mapper     = $mapper;
        $this->serializer = $serializer;
        $this->logger     = $logger;
        $this->config     = $config;
    }

    public function publish(InvoiceInterface $invoice, bool $force = false): void
    {
        if (!$this->config->isEnabled()) {
            $this->logger->debug(
                'TraceDock transaction tracking is not enabled'
            );
            return;
        }

        if ($force || $this->isAllowed($invoice)) {
            $this->publisher->publish(
                self::TOPIC_NAME,
                $this->serializer->serialize(
                    $this->mapper->map($invoice)
                )
            );

            $this->logger->info(
                'TraceDock transaction published for invoice #' . $invoice->getIncrementId()
            );
        }
    }

    private function isAllowed(
        InvoiceInterface $invoice
    ): bool {
        return $invoice instanceof Invoice
            && $invoice->getState() == $invoice::STATE_PAID
            && $invoice->getOrigData(Invoice::STATE) != $invoice->getState();
    }
}
