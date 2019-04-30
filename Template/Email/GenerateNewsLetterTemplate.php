<?php

namespace Extait\Articles\Template\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Extait\Articles\Model\Article;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class GenerateNewsLetterTemplate
 * @package Extait\Articles\Template\Email
 */
class GenerateNewsLetterTemplate
{
    /**
     * Template id name
     */
    const TEMPLATE_ID = 'extait_articles_newsletter_template';

    /**
     * Path to suffix value
     */
    const XML_PATH_ARTICLES_GENERAL = 'articles/general/newsletter_subject';

    /**
     * Sender email config path - from default CONTACT extension
     */
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Article
     */
    protected $_article;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * GenerateNewsLetterTemplate constructor.
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param Article $article
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        Article $article,
        StoreManagerInterface $storeManager
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_article = $article;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $subscribersEmail
     * @param $month
     * @return TransportBuilder
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateTemplate($subscribersEmail, $month)
    {
        $variables = $this->getVariables($month);
        $this->_transportBuilder->setTemplateIdentifier(self::TEMPLATE_ID)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ])
            ->setTemplateVars($variables)
            ->setFrom($this->emailSender())
            ->addTo($subscribersEmail);

        return $this->_transportBuilder;
    }

    /**
     * @param string $month
     * @return array
     */
    protected function getVariables($month)
    {
        $subject = $this->getSubject($month);
        $articles = $this->_article->getLastMonthArticles($month);
        $this->changeContentLength($articles);
        return [
            'subject' => $subject,
            'articles' => $articles,
        ];
    }

    /**
     * @param $month
     * @return string
     */
    protected function getSubject($month)
    {
        $currentDate = mktime(0, 0, 0, $month, 1);
        $monthName = date('F', strtotime('-1 month', $currentDate));
        $subject = $this->getSubjectPrefix();
        $subject = str_replace(['{{', '{', '}}', '}'], [$monthName, $monthName, '', '', ], $subject);
        return $subject;
    }

    /**
     * Get Newsletter subject
     *
     * @return mixed
     */
    protected function getSubjectPrefix()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_ARTICLES_GENERAL,
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    protected function emailSender()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return content with length < 255
     * @param $articles
     * @return string
     */
    protected function changeContentLength($articles)
    {
        if (count($articles) > 0) {
            foreach ($articles as $article) {
                $content = $article->getContent();
                if (strlen($content) < 255) {
                    continue;
                }
                $shortContent = mb_substr($content, 0, 255);
                $str_count = substr_count($shortContent, " ");
                $shortContent = explode(" ", $content);
                $content = '';
                for ($i=0; $i<$str_count; $i++) {
                    $content = $content . $shortContent[$i] . ' ';
                }
                $article->setContent($content . '...');
            }
        }
        return $articles;
    }
}
