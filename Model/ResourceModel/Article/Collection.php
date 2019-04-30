<?php

namespace Extait\Articles\Model\ResourceModel\Article;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Extait\Articles\Model\Article;
use Extait\Articles\Model\ResourceModel\Article as ResourceArticle;

/**
 * Class Collection
 * @package Extait\Articles\Model\ResourceModel\Article
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(Article::class, ResourceArticle::class);
    }

    /**
     * Select related articles
     *
     * @param $productId
     * @return $this
     */
    public function getRelatedArticles($productId)
    {
        $this->getSelect()->join(
            ['related' => $this->getTable('articles_related_products')],
            'main_table.id = related.article_id',
            '*'
        )->where(
            'related.product_id = ?',
            $productId
        );
        return $this;
    }

    /**
     * @param $productId
     * @return $this
     */
    public function getPublishedRelatedArticles($productId)
    {
        $this->getSelect()->join(
            ['related' => $this->getTable('articles_related_products')],
            'main_table.id = related.article_id',
            'main_table.*'
        )->where(
            'related.product_id = ?',
            $productId
        )->where(
            'main_table.published = ?',
            1
        );
        return $this;
    }
}
