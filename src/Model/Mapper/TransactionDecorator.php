<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model\Mapper;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Invoice;
use Tracedock\TransactionTracking\Api\MapperDecoratorInterface;

class TransactionDecorator implements MapperDecoratorInterface
{
    public function decorate(InvoiceInterface $invoice): array
    {
        $order = $invoice instanceof Invoice ? $invoice->getOrder() : null;

        return
            $order ? [
                'transaction_id' => $order->getIncrementId(),
                'transaction_revenue' => $order->getGrandTotal(),
                'transaction_shipping' => $order->getShippingAmount(),
                'transaction_tax' => $order->getTaxAmount(),
                'transaction_currency' => $order->getOrderCurrencyCode(),
                'transaction_coupon' => '',
                'transaction_affiliation' => ''
            ] : [];
    }
}
