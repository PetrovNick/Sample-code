<?php

namespace Extait\Articles\Controller\Articles;

class HomeTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testPageDisplayCorrectArticle()
    {
        $this->dispatch('extait/articles/home/id/17');

        $this->assertContains('test title', $this->getResponse()->getBody());
    }

    public function testPageNotFound()
    {
        $this->dispatch('extait/articles/home/id/18');

        $this->assert404NotFound();
    }
}
