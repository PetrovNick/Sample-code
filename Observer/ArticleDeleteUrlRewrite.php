<?php

namespace Extait\Articles\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlRewriteFactory;

class ArticleDeleteUrlRewrite implements ObserverInterface
{
    /**
     * Articles type
     */
    const ENTITY_TYPE = 'articles';

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * ArticleDeleteUrlRewrite constructor.
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        UrlRewriteFactory $urlRewriteFactory,
        UrlFinderInterface $urlFinder
    ) {
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlFinder = $urlFinder;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $article = $observer->getEvent()->getObject();
        $this->deleteUrlRewrite($article);
    }

    /**
     * @param $article
     * @return bool
     */
    protected function deleteUrlRewrite($article)
    {
        try {
            $rewrite = $this->urlFinder->findOneByData([
                UrlRewrite::ENTITY_ID => $article->getId(),
                UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE
            ]);
            if ($rewrite) {
                $id = $rewrite->getUrlRewriteId();
                $urlRewriteModel = $this->urlRewriteFactory->create()->load($id);
                $urlRewriteModel->delete();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
