<?php

namespace Extait\Articles\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Extait\Articles\Model\ArticlesRelatedFactory;
use Extait\Articles\Model\ResourceModel\ArticlesRelated;

class ProductsRelatedArticle implements ObserverInterface
{
    /**
     * table name
     */
    const TABLE_NAME = 'articles_related_products';
    /**
     * @var ArticlesRelatedFactory
     */
    protected $relatedCollection;

    /**
     * @var ArticlesRelated
     */
    protected $relatedResource;

    /**
     * ArticlesRelatedProduct constructor.
     * @param ArticlesRelatedFactory $relatedCollection
     * @param ArticlesRelated $relatedResource
     */
    public function __construct(
        ArticlesRelatedFactory $relatedCollection,
        ArticlesRelated $relatedResource
    ) {
        $this->relatedCollection = $relatedCollection;
        $this->relatedResource = $relatedResource;
    }

    /**
     * Execute action
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $article = $observer->getEvent()->getObject();
        $articleId = $article->getData('id');
        $products = $this->getProducts();
        $this->setRelatedProducts($articleId, $products);
    }

    /**
     * @return array
     */
    protected function getProducts()
    {
        if (isset($_POST['related_product_listing']['related'])) {
            return $_POST['related_product_listing']['related'];
        }
        return [];
    }

    /**
     * @param $currentCollection
     * @return array
     */
    protected function getCurrentProductsIds($currentCollection)
    {
        $currentProductsIds = [];
        if (!empty($currentCollection->getItems())) {
            foreach ($currentCollection as $item) {
                $currentProductsIds[] = $item->getProductId();
            }
        }
        return $currentProductsIds;
    }

    /**
     * @param $products
     * @return array
     */
    protected function getNewProductsIds($products)
    {
        $newProductsIds = [];
        if (!empty($products)) {
            foreach ($products as $item) {
                $newProductsIds[] = $item['id'];
            }
        }
        return $newProductsIds;
    }

    /**
     * @param $deleteProductsIds
     * @param $currentCollection
     * @return bool
     */
    protected function deleteCurrentRelation($deleteProductsIds, $currentCollection)
    {
        try {
            if (!empty($deleteProductsIds)) {
                foreach ($currentCollection as $item) {
                    if (in_array($item->getProductId(), $deleteProductsIds)) {
                        $item->delete();
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $insertProductsIds
     * @param $articleId
     * @return bool
     */
    protected function insertNewRelations($insertProductsIds, $articleId)
    {
        try {
            // create data array for insert
            if (!empty($insertProductsIds)) {
                foreach ($insertProductsIds as $item) {
                    $data[] = [
                        'product_id' => $item,
                        'article_id' => $articleId,
                    ];
                }
            }
            //multiple insert
            if (isset($data)) {
                $this->relatedResource->insertMultiple(self::TABLE_NAME, $data);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $articleId
     * @param $products
     * @return bool
     */
    protected function setRelatedProducts($articleId, $products)
    {
        try {
            $collection = $this->relatedCollection->create();
            $currentCollection = $collection->getCollection()->addFieldToFilter('article_id', ['eq' => $articleId]);
            $currentProductsIds = $this->getCurrentProductsIds($currentCollection);
            $newProductsIds = $this->getNewProductsIds($products);
            //get products ids  for delete
            $deleteProductsIds = array_diff($currentProductsIds, $newProductsIds);
            //get products ids for insert
            $insertProductsIds = array_diff($newProductsIds, $currentProductsIds);
            $this->deleteCurrentRelation($deleteProductsIds, $currentCollection);
            $this->insertNewRelations($insertProductsIds, $articleId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
