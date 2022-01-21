<?php

declare(strict_types=1);

namespace Tracedock\TransactionTracking\Model\Mapper;

use Magento\Sales\Api\Data\InvoiceInterface;
use Tracedock\TransactionTracking\Api\MapperDecoratorInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\Product;
use Tracedock\TransactionTracking\Model\Config;

class ProductDecorator implements MapperDecoratorInterface
{
    private ProductRepositoryInterface $productRepository;

    private SearchCriteriaBuilder $searchCriteriaBuilder;

    private Config $config;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->productRepository     = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config                = $config;
    }

    public function decorate(InvoiceInterface $invoice): array
    {
        $data       = [];
        $productIds = [];

        foreach ($invoice->getItems() as $item) {
            $productIds[] = $item->getProductId();

            $data[] = [
                'id' => $item->getEntityId(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'quantity' => $item->getQty(),
                'sku' => $item->getSku(),
                'variant' => '',
                'category' => '',
                'coupon' => ''
            ];
        }

        $attributes = $this->config->getAttributes();

        $productList = $this->getProductList($productIds);
        foreach ($productList as $product) {
            foreach ($data as &$item) {
                if (
                    $product instanceof Product
                    && $product->getSku() === $item['sku']
                ) {
                    /**
                     * Do not override the original set value, by setting the
                     * return array of mapProductData first in the array_merge
                     * function.
                     */
                    $item = array_merge(
                        $this->mapProductData($product, $attributes),
                        $item
                    );
                }
            }
        }

        return ['product' => $data];
    }

    /**
     * @return string[]
     */
    private function mapProductData(
        Product $product,
        array $allowedAttributes
    ): array {
        $data = [];

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute[] $attributes */
        $attributes = $product->getAttributes();

        foreach ($product->getData() as $code => $value) {
            if (
                is_scalar($value)
                && isset($attributes[$code])
                && in_array($code, $allowedAttributes)
            ) {
                $data[$code] = $product->getAttributeText($code)
                    ?: $product->getData($code);
            }
        }

        return $data;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    private function getProductList(array $productIds): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'entity_id',
            $productIds,
            'in'
        )->create();

        return $this->productRepository->getList(
            $searchCriteria
        )->getItems();
    }
}
