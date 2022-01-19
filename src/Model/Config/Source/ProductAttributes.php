<?php
declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model\Config\Source;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class ProductAttributes implements OptionSourceInterface
{
    private $eavConfig;

    public function __construct(
        EavConfig $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    public function toOptionArray(): array
    {
        try {
            $type = $this->eavConfig->getEntityType(Product::ENTITY);
        } catch (LocalizedException $e) {
            return [];
        }

        $options             = [];
        $attributeCollection = $type->getAttributeCollection();

        foreach ($attributeCollection as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $options[]     = [
                'value' => $attributeCode,
                'label' => sprintf(
                    '%s [%s]',
                    $attribute->getDefaultFrontendLabel(),
                    $attributeCode
                )
            ];
        }

        usort(
            $options,
            function (array $a, array $b) {
                return strnatcasecmp($a['label'], $b['label']);
            }
        );

        return $options;
    }
}
