<?php

namespace Extait\Articles\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Extait\Articles\Model\ResourceModel\Article\Collection;
use Extait\Articles\Model\Article;

/**
 * Class RelatedArticles
 * @api
 * @package Extait\Articles\Block\Product
 */
class RelatedArticles extends Template
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * RelatedArticles constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Collection $collection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Collection $collection,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->collection = $collection;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    protected function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }


    /**
     * Return related articles
     * @return array|\Magento\Framework\DataObject[]
     */
    public function getArticles()
    {
        $product = $this->getProduct();
        $productId = $product->getEntityId();
        $relatedArticles = $this->collection->getPublishedRelatedArticles($productId)->getItems();
        if (!empty($relatedArticles)) {
            return $relatedArticles;
        }
        return [];
    }

    /**
     * @param $article
     * @return string
     */
    public function getArticleUrl($article)
    {
        if ($article instanceof Article) {
            $url = $article->getUrl();
            return $url;
        }
        return null;
    }
}
