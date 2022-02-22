<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Tracedock\TransactionTracking\Api\ConfigInterface;

class Config implements ConfigInterface
{
    private const CONFIG_PATH = 'tracedock/%s/%s';

    private ScopeConfigInterface $config;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->config = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->config->isSetFlag(
            $this->getConfigPath('enabled'),
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isProductionModeEnabled(): bool
    {
        return $this->config->isSetFlag(
            $this->getConfigPath('production_mode'),
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiUrl(): string
    {
        return (string) $this->config->getValue(
            $this->getConfigPath('api_url'),
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAttributes(): array
    {
        $attributes = (string) $this->config->getValue(
            $this->getConfigPath('attributes'),
            ScopeInterface::SCOPE_STORE
        );

        return array_filter(
            explode(',', $attributes)
        );
    }

    private function getConfigPath(
        string $field,
        string $group = 'general'
    ): string {
        return sprintf(
            self::CONFIG_PATH,
            $group,
            $field
        );
    }
}
