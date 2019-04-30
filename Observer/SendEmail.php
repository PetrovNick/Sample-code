<?php

namespace Extait\Articles\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Extait\Articles\Helper\Email;
use Extait\Articles\Model\ArticlesRelatedFactory;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;

class SendEmail implements ObserverInterface
{
    /**
     * @var Email
     */
    protected $helper;

    /**
     * @var ArticlesRelatedFactory
     */
    protected $related;

    /**
     * SendEmail constructor.
     * @param Email $email
     * @param ArticlesRelatedFactory $related
     */
    public function __construct(
        Email $email,
        ArticlesRelatedFactory $related
    ) {
        $this->helper = $email;
        $this->related = $related;
    }

    /**
     * Execute action
     *
     * @param Observer $observer
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $orderData = $observer->getEvent()->getOrder();
        $customerData = $orderData->getAddresses();
        $customerName = $this->getCustomerName($customerData[0]);
        $customerEmail = $this->getCustomerEmail($customerData[0]);
        $products = $this->getProducts($orderData->getItems());

        if ($products) {
            $this->helper->notify($customerName, $customerEmail, $products);
        }
    }

    /**
     * get fool customer name
     *
     * @param $customerData
     * @return string
     */
    protected function getCustomerName($customerData)
    {
        $name = $customerData->getFirstName() . ' ' . $customerData->getLastname();
        return $name;
    }

    /**
     * @param $customerData
     * @return mixed
     */
    protected function getCustomerEmail($customerData)
    {
        return $customerData->getEmail();
    }

    /**
     * Get products array with related articles
     *
     * @param $items
     * @return array
     */
    protected function getProducts($items)
    {
        $products = [];
        foreach ($items as $product) {
            $productId = $product->getProductId();
            $relatedIds = $this->related->create()->getCollection()
                ->addFieldToFilter('product_id', ['eq' => $productId])
                ->getItems();
            if (!empty($relatedIds)) {
                $products[] = [
                    'product_name' => $product->getName(),
                    'articles_ids' => $relatedIds,
                ];
            }
        }
        return $products;
    }
}
