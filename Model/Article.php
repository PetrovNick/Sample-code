<?php

namespace Extait\Articles\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Extait\Articles\Model\ResourceModel\Article as ResourceArticle;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class Article
 * @package Extait\Articles\Model
 */
class Article extends AbstractModel implements ArticleInterface, IdentityInterface
{

    const CACHE_TAG = 'extait_articles';

    /**
     * Article statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Entity type for article
     */
    const ENTITY_TYPE = 'articles';

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var string
     */
    protected $_eventPrefix = 'extait_articles';

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Article constructor.
     * @param Context $context
     * @param Registry $registry
     * @param UrlFinderInterface $urlFinder
     * @param TimezoneInterface $timezoneInterface
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Context $context,
        Registry $registry,
        UrlFinderInterface $urlFinder,
        TimezoneInterface $timezoneInterface,
        UrlInterface $urlBuilder
    ) {
        $this->urlFinder = $urlFinder;
        $this->timezoneInterface = $timezoneInterface;
        $this->urlBuilder = $urlBuilder;
        parent::__construct(
            $context,
            $registry
        );
    }

    protected function _construct()
    {
        $this->_init(ResourceArticle::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Is Published
     *
     * @return bool
     */
    public function isPublished()
    {
        return (bool)$this->getData(self::PUBLISHED);
    }

    /**
     * Set is Published
     *
     * @param $published
     * @return Article
     */
    public function setPublished($published)
    {
        return $this->setData(self::PUBLISHED, $published);
    }

    /**
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $rewrite = $this->urlFinder->findOneByData([
            UrlRewrite::ENTITY_ID => $this->getId(),
            UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE
            ]);
        if ($rewrite === null) {
            return $rewrite;
        }
        return $this->urlBuilder->getUrl($rewrite->getRequestPath());
    }

    /**
     * @param $month
     * @return \Magento\Framework\DataObject[]
     */
    public function getLastMonthArticles($month)
    {
        //get first and last days
        $currentDate = mktime(0, 0, 0, $month, 1);
        $previousMonth = strtotime('-1 month', $currentDate);
        $firstDay = date('Y-m-01', $previousMonth);
        $lastDay = date('Y-m-t', $previousMonth);

        $collection = $this->getCollection()
            ->addFieldToFilter('published', ['eq' => 1])
            ->addFieldToFilter('published_at', ['from'=>$firstDay, 'to'=>$lastDay])
            ->getItems();
        return $collection;
    }

    /**
     * Change date format
     *
     * @return string
     */
    public function getTimeAccordingToTimeZone()
    {
        $newDate = $this->timezoneInterface->formatDateTime(
            $this->getPublishedAt(),
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::NONE
        );
        return $newDate;
    }
}
