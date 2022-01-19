<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Api;

interface ConfigInterface
{
    public function isEnabled(): bool;

    public function isProductionModeEnabled(): bool;

    public function getApiUrl(): string;
}
