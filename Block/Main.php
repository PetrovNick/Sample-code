<?php

namespace Extait\Articles\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Extait\Articles\Model\ArticleFactory;
use Extait\Articles\Model\Article;

/**
 * Class Main
 * @api
 * @package Extait\Articles\Block
 */
class Main extends Template
{
    /**
     * @var ArticleFactory
     */
    protected $articleFactory;

    /**
     * Main constructor.
     * @param Context $context
     * @param ArticleFactory $articleFactory
     */
    public function __construct(
        Context $context,
        ArticleFactory $articleFactory
    ) {
        $this->articleFactory = $articleFactory;
        parent::__construct($context);
    }

    /**
     * Select 3 last published articles
     * @return mixed
     */
    public function getArticles()
    {
        $article = $this->articleFactory->create();
        $collection = $article->getCollection();
        $collection->setOrder('published_at', 'DESC');
        $collection->setPageSize(3);
        return $collection;
    }

    /**
     * @param $article
     * @return string
     */
    public function getArticleUrl($article)
    {
        try {
            if ($article instanceof Article) {
                $url = $article->getUrl();
                if ($url !== null) {
                    return $url;    
                }
                return $this->getUrl('extait/articles/home', ['id' => $article->getId()]);
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
