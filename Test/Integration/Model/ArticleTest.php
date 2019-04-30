<?php

namespace Extait\Articles\Test\Integration\Model;

/**
 * Class ArticleTest
 * @package Extait\Articles\Test\Integration\Model
 * @magentoDbIsolation disabled
 */
class ArticleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Extait\Articles\Model\Article
     */
    protected $_model;

    public static function loadFixture()
    {
        include __DIR__ . '/../_files/articles.php';
    }

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Extait\Articles\Model\Article::class
        );
    }

//    /**
//     * @magentoDataFixture loadFixture
//     */
    public function testSetPublishedTrue()
    {
        //set published 1 and verify
        $this->_model->load(17);
        $this->_model->setPublished(1)->save();
        $this->assertEquals(1, $this->_model->isPublished());
    }

    public function testSetPublishedFalse()
    {
        //set published 0 and verify
        $this->_model->load(17);
        $this->_model->setPublished(0)->save();
        $this->assertEquals(0, $this->_model->isPublished());
    }
}
