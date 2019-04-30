<?php

namespace Extait\Articles\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Extait\Articles\Template\Email\GenerateNewsLetterTemplate;
use Magento\Framework\Mail\Template\TransportBuilder;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class NewsLetter
 * @package Extait\Articles\Helper
 */
class NewsLetter extends AbstractHelper
{
    /**
     * @var GenerateNewsLetterTemplate
     */
    protected $_newsLetterTemplate;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * NewsLetter constructor.
     * @param Context $context
     * @param GenerateNewsLetterTemplate $generateNewsLetterTemplate
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        GenerateNewsLetterTemplate $generateNewsLetterTemplate,
        TransportBuilder $transportBuilder
    ) {
        $this->_newsLetterTemplate = $generateNewsLetterTemplate;
        $this->_transportBuilder = $transportBuilder;
        parent::__construct($context);
    }

    /**
     * @param array $subscribersEmail
     * @param string $month
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function notify($subscribersEmail, $month='')
    {
        //get current month number
        if (empty($month))  {
            $month = date('m');
        }
        if ((int)$month > 0 && (int)$month < 13) {
            $this->_transportBuilder = $this->_newsLetterTemplate->generateTemplate($subscribersEmail, $month);
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        } else {
            throw new InvalidArgumentException('Invalid -m argument. Need number from 1 to 12');
        }
    }
}
