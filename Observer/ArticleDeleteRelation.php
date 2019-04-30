<?php

namespace Extait\Articles\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Extait\Articles\Model\ArticlesRelatedFactory;

/**
 * Class ArticleDeleteRelation
 * @package Extait\Articles\Observer
 */
class ArticleDeleteRelation implements ObserverInterface
{
    /**
     * @var ArticlesRelatedFactory
     */
    protected $relatedCollection;

    /**
     * ArticleDeleteRelation constructor.
     * @param ArticlesRelatedFactory $relatedCollection
     */
    public function __construct(
        ArticlesRelatedFactory $relatedCollection
    ) {
        $this->relatedCollection = $relatedCollection;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $article = $observer->getEvent()->getObject();
        $this->deleteRelation($article);
    }

    /**
     * Delete rows with this article
     *
     * @param $article
     * @return bool
     */
    protected function deleteRelation($article)
    {
        try {
            $collection = $this->relatedCollection->create();
            $articleId = $article->getId();
            $collection = $collection->getCollection()->addFieldToFilter('article_id', ['eq' => $articleId]);
            foreach ($collection as $item) {
                $item->delete();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
