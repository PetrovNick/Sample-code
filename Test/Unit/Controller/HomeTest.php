<?php

namespace Extait\Articles\Test\Unit\Controller;

class HomeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Extait\Articles\Controller\Articles\Home
     */
    private $homeController;

    /**
     * @var \Extait\Articles\Model\ArticleFactory
     */
    private $articleFactoryMock;

    /**
     * @var \Extait\Articles\Model\Article
     */
    private $articleModelMock;

    /**
     * @var \Extait\Articles\Model\ArticlesRelatedFactory
     */
    private $relatedFactoryMock;

    public function setUp()
    {
        $this->articleFactoryMock = $this->createMock(\Extait\Articles\Model\ArticleFactory::class);
        $this->articleModelMock = $this->createMock(\Extait\Articles\Model\Article::class);
        $this->relatedFactoryMock = $this->createMock(\Extait\Articles\Model\ArticlesRelatedFactory::class);

        $ObjectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->homeController = $ObjectManagerHelper->getObject(
            'Extait\Articles\Controller\Articles\Home',
            [
                'articleFactory' => $this->articleFactoryMock,
                'relatedCollection' => $this->relatedFactoryMock,
            ]
        );
    }

    public function testExecuteWithExpectedException()
    {
        $this->expectException(
            '\Magento\Framework\Exception\NotFoundException'
        );
        $this->articleFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->articleModelMock));
        $this->articleModelMock->expects($this->once())->method('load')->will($this->returnValue($this->articleModelMock));
        $this->articleModelMock->expects($this->once())->method('getData')->will($this->returnValue(null));
        $this->homeController->execute();
    }
}
