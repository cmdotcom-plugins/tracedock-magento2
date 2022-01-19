<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model;

use Magento\Sales\Api\Data\InvoiceInterface;

class Mapper
{
    private array $mapperDecorators;

    public function __construct(
        array $mapperDecorators
    ) {
        $this->mapperDecorators = $mapperDecorators;
    }

    public function map(
        InvoiceInterface $invoice
    ): array {
        $data = [];

        foreach ($this->mapperDecorators as $decorator) {
            $data = array_merge(
                $data,
                $decorator->decorate(
                    $invoice,
                    $data
                )
            );
        }

        return $data;
    }
}
