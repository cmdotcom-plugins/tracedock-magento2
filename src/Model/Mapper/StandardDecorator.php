<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model\Mapper;

use Magento\Sales\Api\Data\InvoiceInterface;
use Tracedock\TransactionTracking\Api\MapperDecoratorInterface;
use Tracedock\TransactionTracking\Api\ConfigInterface;
use Magento\Sales\Model\Order\Invoice;

class StandardDecorator implements MapperDecoratorInterface
{
    private ConfigInterface $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function decorate(InvoiceInterface $invoice): array
    {
        $mode = $this->config->isProductionModeEnabled()
            ? 'production'
            : 'test';

        $quoteId = $invoice instanceof Invoice
            ? $invoice->getOrder()->getQuoteId()
            : 0;

        /*
         * Magento does not contain a default userId,
         * as such we use the quoteId to stitch with the browser session.
         * For compatibility with the template installation we forwards both
         * fields to TraceDock endpoint.
         */

        return [
            'env'      => $mode,
            'quoteId' => $quoteId,
            'userId' => $quoteId,
        ];
    }
}
