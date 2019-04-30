<?php

namespace Extait\Articles\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Extait\Articles\Model\ResourceModel\ArticlesRelated as Related;

/**
 * Class ArticlesRelated
 * @package Extait\Articles\Model
 */
class ArticlesRelated extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'articles_related_product';

    protected function _construct()
    {
        $this->_init(Related::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
