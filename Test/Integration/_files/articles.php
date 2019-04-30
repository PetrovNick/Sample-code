<?php

/**
 * @var \Extait\Articles\Model\Article $article
 */
$article = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Extait\Articles\Model\Article::class);
$article->setTitle('test title')
    ->setAuthor('test author')
    ->setContent('test content')
    ->setCreatedAt('2019-01-12 18:12:26')
    ->setUpdatedAt('2019-01-12 18:12:26')
    ->setPublishedAt('2019-01-12 18:12:26')
    ->setPublished(0)
    ->save();
