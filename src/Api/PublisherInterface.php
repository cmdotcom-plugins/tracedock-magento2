<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Api;

use Magento\Sales\Api\Data\InvoiceInterface;

interface PublisherInterface
{
    public function publish(
        InvoiceInterface $invoice,
        bool $force = false
    ): void;
}
