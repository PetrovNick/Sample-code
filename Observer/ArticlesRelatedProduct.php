<?php

namespace Extait\Articles\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Extait\Articles\Model\ArticlesRelatedFactory;
use Extait\Articles\Model\ResourceModel\ArticlesRelated;
use Magento\Framework\App\RequestInterface;

/**
 * Class ArticlesRelatedProduct
 * @package Extait\Articles\Observer
 */
class ArticlesRelatedProduct implements ObserverInterface
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
     * @var RequestInterface
     */
    protected $requestInterface;

    /**
     * ArticlesRelatedProduct constructor.
     * @param ArticlesRelatedFactory $relatedCollection
     * @param ArticlesRelated $relatedResource
     * @param RequestInterface $requestInterface
     */
    public function __construct(
        ArticlesRelatedFactory $relatedCollection,
        ArticlesRelated $relatedResource,
        RequestInterface $requestInterface
    ) {
        $this->relatedCollection = $relatedCollection;
        $this->relatedResource = $relatedResource;
        $this->requestInterface = $requestInterface;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
         $product = $observer->getData();
         $productId = $this->getProductId($product);
         $articles = $this->getArticles();
         $this->setRelatedArticles($productId, $articles);
    }

    /**
     * @param $product
     * @return mixed
     */
    protected function getProductId($product)
    {
        $product = $product['product']->getData();
        return $product['entity_id'] ? $product['entity_id'] : 0;
    }

    /**
     * get articles related product
     *
     * @return mixed
     */
    protected function getArticles()
    {
        $resultArticles = [];
        $articles = $this->requestInterface->getParam('links');
        if (!empty($articles)) {
            $resultArticles = $articles['article'];
        }
        return $resultArticles;
    }

    /**
     * @param $articles
     * @return array
     */
    protected function getNewArticlesIds($articles)
    {
        $newArticlesIds = [];
        if (!empty($articles)) {
            foreach ($articles as $item) {
                $newArticlesIds[] = $item['id'];
            }
        }
        return $newArticlesIds;
    }

    /**
     * @param $currentCollection
     * @return array
     */
    protected function getCurrentArticlesIds($currentCollection)
    {
        $currentArticlesIds = [];
        if (!empty($currentCollection->getItems())) {
            foreach ($currentCollection as $item) {
                $currentArticlesIds[] = $item->getArticleId();
            }
        }
        return $currentArticlesIds;
    }

    /**
     * @param $deleteArticlesIds
     * @param $currentCollection
     * @return bool
     */
    protected function deleteCurrentRelation($deleteArticlesIds, $currentCollection)
    {
        try {
            if (!empty($deleteArticlesIds)) {
                foreach ($currentCollection as $item) {
                    if (in_array($item->getArticleId(), $deleteArticlesIds)) {
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
     * @param $insertArticlesIds
     * @param $productId
     * @return bool
     */
    protected function insertNewRelation($insertArticlesIds, $productId)
    {
        try {
            // create data array for insert
            if (!empty($insertArticlesIds)) {
                foreach ($insertArticlesIds as $item) {
                    $data[] = [
                        'article_id' => $item,
                        'product_id' => $productId,
                    ];
                }
            }
            if (isset($data)) {
                $this->relatedResource->insertMultiple(self::TABLE_NAME, $data);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $productId
     * @param $articles
     * @return bool
     */
    protected function setRelatedArticles($productId, $articles)
    {
        try {
            $collection = $this->relatedCollection->create();
            $currentCollection = $collection->getCollection()->addFieldToFilter('product_id', ['eq' => $productId]);
            $currentArticlesIds = $this->getCurrentArticlesIds($currentCollection);
            $newArticlesIds = $this->getNewArticlesIds($articles);
            //get articles ids  for delete
            $deleteArticlesIds = array_diff($currentArticlesIds, $newArticlesIds);
            //get articles ids for insert
            $insertArticlesIds = array_diff($newArticlesIds, $currentArticlesIds);
            $this->deleteCurrentRelation($deleteArticlesIds, $currentCollection);
            $this->insertNewRelation($insertArticlesIds, $productId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
