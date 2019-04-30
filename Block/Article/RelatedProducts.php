<?php

namespace Extait\Articles\Block\Article;

use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Pricing\Helper\Data;

/**
 * Class RelatedProducts
 * @api
 * @package Extait\Articles\Block\Article
 */
class RelatedProducts extends Template
{
    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * @var Data
     */
    protected $_priceHelper;

    /**
     * RelatedProducts constructor.
     * @param Context $context
     * @param Image $imageHelper
     * @param Data $priceHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Image $imageHelper,
        Data $priceHelper,
        array $data = []
    ) {
        $this->_imageHelper = $imageHelper;
        $this->_priceHelper = $priceHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param $product
     * @return string
     */
    public function getImageUrl($product)
    {
        $imageUrl = $this->_imageHelper->init($product, 'product_page_image_small')
            ->setImageFile($product->getFile())->resize(200, 200)->getUrl();
        return $imageUrl;
    }

    /**
     * @param $product
     * @return float|string
     */
    public function getFormattedPrice($product)
    {
        $formattedPrice = $this->_priceHelper->currency($product->getPrice(), true, false);
        return $formattedPrice;
    }
}
