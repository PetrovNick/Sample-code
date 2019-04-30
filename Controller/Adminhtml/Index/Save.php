<?php

namespace Extait\Articles\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Extait\Articles\Model\ArticleFactory;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class Save
 * @package Extait\Articles\Controller\Adminhtml\Index
 */
class Save extends Action
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
     * @var ArticleFactory
     */
    protected $articleFactory;

    /**
     * Save constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param ArticleFactory $articleFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        ArticleFactory $articleFactory
    ) {
        $this->articleFactory = $articleFactory;
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!empty($data)) {
            $article = $this->articleFactory->create();
            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $article = $article->load($id);
            }
            try {
                $article->setData($data)->save();
                $this->messageManager->addSuccessMessage(__('The article has been saved.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Failed to save.'));
            }

            if ($this->getRequest()->getParam('id')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $article->getId()]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
