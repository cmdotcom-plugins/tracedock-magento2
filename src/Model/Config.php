<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Tracedock\TransactionTracking\Api\ConfigInterface;

/**
 * Class Config
 *
 * @package Tracedock\TransactionTracking\Model
 */
class Config implements ConfigInterface
{
    private const CONFIG_PATH = 'tracedock/%s/%s';

    private ScopeConfigInterface $config;

    private StoreManagerInterface $storeManager;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->config = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isSetFlag(
            $this->getConfigPath('enabled'),
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * @return bool
     */
    public function isProductionModeEnabled(): bool
    {
        return $this->config->isSetFlag(
            $this->getConfigPath('production_mode'),
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return (string)$this->config->getValue(
            $this->getConfigPath('api_url'),
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = (string)$this->config->getValue(
            $this->getConfigPath('attributes'),
            ScopeInterface::SCOPE_STORE
        );

        return array_filter(
            explode(',', $attributes)
        );
    }

    /**
     * @param string $field
     * @param string $group
     *
     * @return string
     */
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

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
