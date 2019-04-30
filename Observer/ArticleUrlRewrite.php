<?php

namespace Extait\Articles\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;

class ArticleUrlRewrite implements ObserverInterface
{
    /**
     * Articles type
     */
    const ENTITY_TYPE = 'articles';

    /**
     * Path to suffix value
     */
    const XML_PATH_ARTICLES_GENERAL = 'articles/general/url_suffix';

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ArticleUrlRewrite constructor.
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param UrlFinderInterface $urlFinder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlRewriteFactory $urlRewriteFactory,
        UrlFinderInterface $urlFinder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlFinder = $urlFinder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $article = $observer->getEvent()->getObject();
        $this->createUrlRewrite($article);
    }

    /**
     * Create new url or edit current
     * @param $article
     * @return bool
     */
    protected function createUrlRewrite($article)
    {
        try {
            $rewrite = $this->urlFinder->findOneByData([
                UrlRewrite::ENTITY_ID => $article->getId(),
                UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE
            ]);
            $urlRewriteModel = $this->urlRewriteFactory->create();
            if (!empty($rewrite)) {
                $id = $rewrite->getUrlRewriteId();
                $urlRewriteModel = $urlRewriteModel->load($id);
            }
            $suffix = $this->getUrlSuffix();
            $friendlyUrl = str_replace(" ", "-", strtolower($article->getTitle()));
            $urlRewriteModel->setStoreId($this->storeManager->getStore()->getId())
                ->setIsSystem(0)
                ->setEntityId($article->getId())
                ->setEntityType(self::ENTITY_TYPE)
                ->setTargetPath("/extait/articles/home/id/" . $article->getId())
                ->setRequestPath($friendlyUrl . $suffix)
                ->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get Suffix for Url
     *
     * @return mixed
     */
    protected function getUrlSuffix()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $suffix = $this->scopeConfig->getValue(self::XML_PATH_ARTICLES_GENERAL, $storeScope);
        if ($suffix != '' && $suffix[0] != '.') {
            return '.' . $suffix;
        } else {
            return $suffix;
        }
    }
}
