<?php

namespace Extait\Articles\Block\Adminhtml\Article\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 * @package Extait\Articles\Block\Adminhtml\Article\Edit
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * print Save button
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Article'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
