<?php

namespace Extait\Articles\Plugin;

use Magento\Config\Model\Config;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigPlugin
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
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\UrlRewrite\Model\UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ConfigPlugin constructor.
     * @param UrlPersistInterface $urlPersist
     * @param UrlFinderInterface $urlFinder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        UrlFinderInterface $urlFinder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->urlPersist = $urlPersist;
        $this->urlFinder = $urlFinder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $result
     * @return bool
     */
    public function afterSave(Config $result)
    {
        try {
            $suffix = $this->getSuffix($result);
            $this->changeUrlRewrite($suffix);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get new suffix
     *
     * @param $result
     * @return mixed
     */
    protected function getSuffix($result)
    {
        $suffix = $result->getData('groups');
        if (isset($suffix['general']['fields']['url_suffix']['value'])) {
            $suffix = $suffix['general']['fields']['url_suffix']['value'];
            if ($suffix != '' && $suffix[0] != '.') {
                return '.' . $suffix;
            } else {
                return $suffix;
            }
        } else {
            $storeScope = ScopeInterface::SCOPE_STORE;
            return $this->scopeConfig->getValue(self::XML_PATH_ARTICLES_GENERAL, $storeScope);
        }
    }

    /**
     * @param $suffix
     * @throws \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException
     */
    protected function changeUrlRewrite($suffix)
    {
        $rewrite = $this->urlFinder->findAllByData([UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE]);
        foreach ($rewrite as $item) {
            $path = $item->getRequestPath();
            $newPath = explode('.', $path);
            $newPath[1] = $suffix;
            $newPath = implode($newPath);
            $item->setRequestPath(strtolower($newPath));
        }
        $this->urlPersist->replace($rewrite);
    }
}
