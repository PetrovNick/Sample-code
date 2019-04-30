<?php

namespace Extait\Articles\Cron;

use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory;
use Extait\Articles\Helper\NewsLetter as Helper;
use Extait\Articles\Model\ArticleFactory;

/**
 * Class MailSender
 * @package Extait\Articles\Cron
 */
class MailSender
{
    /**
     * @var ArticleFactory
     */
    protected $_articleFactory;
    /**
     * @var CollectionFactory
     */
    protected $_subscribeCollection;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * MailSender constructor.
     * @param CollectionFactory $subscribeCollectionFactory
     * @param Helper $helper
     * @param ArticleFactory $articleFactory
     */
    public function __construct(
        CollectionFactory $subscribeCollectionFactory,
        Helper $helper,
        ArticleFactory $articleFactory
    ) {
        $this->_subscribeCollection = $subscribeCollectionFactory;
        $this->_helper = $helper;
        $this->_articleFactory = $articleFactory;
    }

    /**
     * Execute action
     * @return bool
     */
    public function execute()
    {
        try {
            $subscribers = $this->_subscribeCollection->create()->useOnlySubscribed()->getItems();
            if (count($subscribers) > 0) {
                $subscribersEmail = [];
                foreach ($subscribers as $item) {
                    $subscribersEmail[] = $item->getSubscriberEmail();
                }
                $this->_helper->notify($subscribersEmail);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
