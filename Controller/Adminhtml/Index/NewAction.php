<?php

namespace Extait\Articles\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class NewAction
 * @package Extait\Articles\Controller\Adminhtml\Index
 */
class NewAction extends Action
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
     * NewAction constructor.
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
        $page_object->getConfig()->getTitle()->prepend(__('New Article'));
        return $page_object;
    }
}
