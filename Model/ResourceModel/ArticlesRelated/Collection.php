<?php

namespace Extait\Articles\Model\ResourceModel\ArticlesRelated;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Extait\Articles\Model\ArticlesRelated;
use Extait\Articles\Model\ResourceModel\ArticlesRelated as RelatedResource;

/**
 * Class Collection
 * @package Extait\Articles\Model\ResourceModel\ArticlesRelated
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ArticlesRelated::class, RelatedResource::class);
    }
}
