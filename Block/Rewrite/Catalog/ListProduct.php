<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   Innovadel Technologies Ltd.
 * @package    Innovadeltech_Convertopia
 * @subpackage etc
 * @author     Innovadel Technologies <support@innovadeltech.com>
 * @copyright  Copyright (c) 2019 Innovadel Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 */

namespace Innovadeltech\Convertopia\Block\Rewrite\Catalog;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;

use Magento\Framework\Data\Helper\PostHelper;

use Magento\Framework\Url\Helper\Data;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var string|null
     */
    protected $_q;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * ListProduct constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper
        );
    }

    /**
     * Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_q = $this->request->getParam('q');
    }

    /**
     * Get product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getProductCollection()
    {
        if ($this->_q) {
            $collection = $this->collectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addFieldToFilter('entity_id', ['in' => $this->_q]);
            return $collection;
        } else {
            return parent::_getProductCollection();
        }
    }

    /**
     * Get loaded product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getLoadedProductCollection()
    {
        if ($this->_q) {
            $collection = $this->collectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addFieldToFilter('entity_id', ['in' => $this->_q]);
            return $collection;
        } else {
            return $this->_getProductCollection();
        }
    }
}
