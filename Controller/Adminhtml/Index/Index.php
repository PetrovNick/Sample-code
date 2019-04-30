<?php

namespace Extait\Articles\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Extait\Articles\Controller\Adminhtml\Index
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * Init action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page_object = $this->pageFactory->create();
        $page_object->getConfig()->getTitle()->prepend(__('Articles'));
        return $page_object;
    }
}
