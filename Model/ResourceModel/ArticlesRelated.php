<?php

namespace Extait\Articles\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ArticlesRelated
 * @package Extait\Articles\Model\ResourceModel
 */
class ArticlesRelated extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('articles_related_products', 'id');
    }

    /**
     * Insert multiple rows
     *
     * @param $table
     * @param $data
     * @return bool
     */
    public function insertMultiple($table, $data)
    {
        try {
            $connection = $this->getConnection();
            $connection->insertMultiple($table, $data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
