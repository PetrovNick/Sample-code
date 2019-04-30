<?php

namespace Extait\Articles\Controller\Articles;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Extait\Articles\Model\ArticleFactory;
use Extait\Articles\Model\ArticlesRelatedFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Action;

/**
 * Class Home
 * @package Extait\Articles\Controller\Articles
 *
 */
class Home extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var ArticleFactory
     */
    protected $articleFactory;

    /**
     * @var ArticlesRelatedFactory
     */
    protected $relatedCollection;

    /**
     * @var
     */
    protected $relatedProduct;

    /**
     * Home constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param ArticleFactory $articleFactory
     * @param ArticlesRelatedFactory $relatedCollection
     * @param ProductFactory $relatedProduct
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ArticleFactory $articleFactory,
        ArticlesRelatedFactory $relatedCollection,
        ProductFactory $relatedProduct
    ) {
        $this->pageFactory = $pageFactory;
        $this->articleFactory = $articleFactory;
        $this->relatedCollection = $relatedCollection;
        $this->relatedProduct = $relatedProduct;
        return parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function getArticle()
    {
        $id = $this->getRequest()->getParam('id');
        $article = $this->articleFactory->create();
        $article = $article->load($id);
        if ($article->getData()) {
            return $article;
        }
        return false;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $page_object = $this->pageFactory->create();
        $article = $this->getArticle();
        if ($article === false) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page Not Found'));
        }
        $relatedProducts = $this->getRelatedProducts($article->getId());
        $title = $article->getTitle();
        $newDate = $article->getTimeAccordingToTimeZone();
        $article->setPublishedAt($newDate);
        $page_object->getConfig()->getTitle()->set($title);
        $page_object->getLayout()->getBlock('extait_articles')->setArticle($article);
        if ($relatedProducts) {
            $page_object->getLayout()->getBlock('related_products')->setRelatedProducts($relatedProducts);
        }
        return $page_object;
    }

    /**
     * Get related products
     *
     * @param $articleId
     * @return mixed
     */
    protected function getRelatedProducts($articleId)
    {
        $collection = $this->relatedCollection->create();
        $collection = $collection->getCollection()->addFieldToFilter('article_id', ['eq' => $articleId])->getItems();
        if (count($collection) > 0) {
            $productsIds = [];
            foreach ($collection as $item) {
                $productsIds[] = $item->getProductId();
            }
            return $this->relatedProduct->create()
                ->getCollection()
                ->addFieldToFilter('entity_id', ['in' => $productsIds])
                ->addAttributeToSelect('*')
                ->getItems();
        }
        return false;
    }
}
