<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Api;

use Magento\Sales\Api\Data\InvoiceInterface;

interface MapperDecoratorInterface
{
    public function decorate(
        InvoiceInterface $invoice
    ): array;
}
