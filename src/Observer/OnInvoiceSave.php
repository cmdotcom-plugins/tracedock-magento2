<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Observer;

use Magento\Framework\Event\{Observer, ObserverInterface};
use Magento\Sales\Api\Data\InvoiceInterface;
use Tracedock\TransactionTracking\Api\PublisherInterface;

class OnInvoiceSave implements ObserverInterface
{
    private PublisherInterface $publisher;

    public function __construct(
        PublisherInterface $publisher
    ) {
        $this->publisher = $publisher;
    }

    public function execute(Observer $observer): void
    {
        $invoice = $observer->getEvent()->getData('invoice');

        if ($invoice instanceof InvoiceInterface) {
            $this->publisher->publish($invoice);
        }
    }
}
