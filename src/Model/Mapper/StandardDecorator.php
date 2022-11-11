<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model\Mapper;

use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Tracedock\TransactionTracking\Api\MapperDecoratorInterface;
use Tracedock\TransactionTracking\Api\ConfigInterface;
use Magento\Sales\Model\Order\Invoice;

class StandardDecorator implements MapperDecoratorInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var QuoteIdToMaskedQuoteIdInterface
     */
    private QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId;

    /**
     * StandardDecorator constructor.
     * @param ConfigInterface $config
     * @param QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId
     */
    public function __construct(
        ConfigInterface $config,
        QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId
    ) {
        $this->config = $config;
        $this->quoteIdToMaskedQuoteId = $quoteIdToMaskedQuoteId;
    }

    /**
     * Decorate payload with standard information.
     *
     * @param InvoiceInterface $invoice
     * @return array
     */
    public function decorate(InvoiceInterface $invoice): array
    {
        $mode = $this->config->isProductionModeEnabled()
            ? 'production'
            : 'test';

        $quoteId = $invoice instanceof Invoice
            ? $invoice->getOrder()->getQuoteId()
            : 0;
        $maskedQuoteId = $this->quoteIdToMaskedQuoteId->execute((int)$quoteId);

        $tracedockApiUrl = $invoice instanceof Invoice ? $invoice->getExtensionAttributes()->getTracedockApiUrl() : '';

        /*
         * Magento does not contain a default userId,
         * as such we use the masked quoteId to stitch with the browser session.
         * For compatibility with the template installation we forward both
         * fields to TraceDock endpoint.
         */

        return [
            'env'             => $mode,
            'quoteId'         => $maskedQuoteId,
            'userId'          => $maskedQuoteId,
            'tracedockApiUrl' => $tracedockApiUrl,
        ];
    }
}
