<?php

namespace Extait\Articles\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Article
 * @package Extait\Articles\Model\ResourceModel
 */
class Article extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('articles', 'id');
    }
}
