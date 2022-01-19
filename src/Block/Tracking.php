<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Block;

use Tracedock\TransactionTracking\Api\ConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Tracking extends Template
{
    private ConfigInterface $config;

    public function __construct(
        ConfigInterface $config,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    public function toHtml(): string
    {
        return $this->config->isEnabled()
            ? parent::toHtml()
            : '';
    }
}
