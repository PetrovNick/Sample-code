<?php

namespace Extait\Articles\Model\Article\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Extait\Articles\Model\Article;

/**
 * Class IsActive
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var Article
     */
    protected $article;

    /**
     * IsActive constructor.
     * @param Article $article
     */
    public function __construct(
        Article $article
    ) {
        $this->article = $article;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->article->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
