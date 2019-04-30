<?php

namespace Extait\Articles\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Extait\Articles\Model\ArticleFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Area;

class Email extends AbstractHelper
{
    /**
     * Sender email config path - from default CONTACT extension
     */
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * @var Context
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var string
     */
    protected $temp_id;

    /**
     * @var ArticleFactory
     */
    protected $articleFactory;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Email constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param ArticleFactory $articleFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ArticleFactory $articleFactory,
        UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $context;
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->urlBuilder = $urlBuilder;
        $this->articleFactory = $articleFactory;
    }

    /**
     * @param $variable
     * @param $receiverInfo
     * @param $templateId
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function generateTemplate($variable, $receiverInfo, $templateId)
    {
        $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($variable)
            ->setFrom($this->emailSender())
            ->addTo($receiverInfo['email'], $receiverInfo['name']);

        return $this;
    }

    /**
     * Return email for sender header
     * @return mixed
     */
    protected function emailSender()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $product
     * @return string
     */
    protected function getSubject($product)
    {
        $productName = str_replace(' ', '-', $product['product_name']);
        return $productName . ':Few Interesting Facts!';
    }

    /**
     * @param $articles
     * @return array
     */
    protected function getArticlesUrl($articles)
    {
        $articlesIds = [];
        foreach ($articles as $article) {
            $articlesIds[] = $article->getArticleId();
        }
        $articles = $this->articleFactory->create()->getCollection()->addFieldToFilter('id', ['in' => $articlesIds])->getItems();
        $articleTitleUrl = [];
        foreach ($articles as $article) {
            $articleTitleUrl[] = [
                'title' => $article->getTitle(),
                'url' => $this->urlBuilder->getUrl($article->getUrl()),
            ];
        }
        return $articleTitleUrl;
    }

    /**
     * @param $name
     * @param $email
     * @param $products
     * @return $this
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function notify($name, $email, $products)
    {
        /* Receiver Detail */
        $receiverInfo = [
            'name' => $name,
            'email' => $email
        ];
        foreach ($products as $product) {
            $variable = [
                'subject' => $this->getSubject($product),
                'articles' => $this->getArticlesUrl($product['articles_ids']),
            ];
            $templateId = "extait_articles_email_template_notification";
            $this->generateTemplate($variable, $receiverInfo, $templateId);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        }
        return $this;
    }
}
