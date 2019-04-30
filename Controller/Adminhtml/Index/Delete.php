<?php

namespace Extait\Articles\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Extait\Articles\Model\ArticleFactory;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class Delete
 * @package Extait\Articles\Controller\Adminhtml\Index
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::admin';

    /**
     * @var ArticleFactory
     */
    protected $articleFactory;

    /**
     * Delete constructor.
     * @param Context $context
     * @param ArticleFactory $articleFactory
     */
    public function __construct(
        Context $context,
        ArticleFactory $articleFactory
    ) {
        $this->articleFactory = $articleFactory;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $article = $this->articleFactory->create()->load($id);
            $article->delete();
            $this->messageManager->addSuccessMessage(__('The article has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Failed to delete.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
