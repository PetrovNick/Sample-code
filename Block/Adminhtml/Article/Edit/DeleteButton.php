<?php

namespace Extait\Articles\Block\Adminhtml\Article\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 * @package Extait\Articles\Block\Adminhtml\Article\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get article id
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getArticleId()) {
            $data = [
                'label' => __('Delete Article'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getArticleId()]);
    }
}
