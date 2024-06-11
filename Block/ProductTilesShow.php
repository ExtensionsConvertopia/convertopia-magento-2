<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Innovadeltech\Convertopia\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;




class ProductTilesShow extends \Magento\Catalog\Block\Product\ListProduct
{

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var $_storeManager
     */
    protected $_storeManager;

    /**
     * @var $scopeConfig
     */
    protected $scopeConfig;
    protected $ProductTilesShowModel;
    /**
     * Feature constructor.
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Innovadeltech\Convertopia\Model\Products\ProductTilesShowModel $ProductTilesShowModel,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->_storeManager = $_storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->ProductTilesShowModel = $ProductTilesShowModel;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        //Your block code
        $currentPage = $this->_request->getFullActionName();
        // $getConfiguration = $this->getConfig("convertopia/product_list/page_display");
        $getConfiguration = $this->_scopeConfig->getValue('convertopia_recommendations/product_list/page_display');

        if (!is_null($getConfiguration)) {
            $getConfiguration = explode(',', $getConfiguration);
        } else {
            $getConfiguration = [];
        }

        if (in_array($currentPage, $getConfiguration)) {
            return true;
        }
        return false;
    }

    public function getTitle()
    {
        $showTitle = $this->ProductTilesShowModel->getTitle();

        return $showTitle;
    }

    public function canShowListing()
    {
        $canShowListing = $this->ProductTilesShowModel->canShowListing();

        return $canShowListing;
    }


    public function ProductTilesHtml($Formkey)
    {
        $ProductTilesShowHtml = $this->ProductTilesShowModel->getProductTilesHTML($Formkey);

        return $ProductTilesShowHtml;
    }
    public function ProductWidgetTilesHtml()
    {
        $ProductTilesShowHtml = $this->ProductTilesShowModel->getWidgetProductHtml();

        return $ProductTilesShowHtml;
    }
}
