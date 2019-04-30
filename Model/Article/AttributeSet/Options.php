<?php

namespace Extait\Articles\Model\Article\AttributeSet;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var null|array
     */
    protected $options;

    /**
     * Options constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (null == $this->options) {
            $this->options = [
                ['label' => __('Yes'), 'value' => 1],
                ['label' => __('No'), 'value' => 0],
            ];
        }
        return $this->options;
    }
}
