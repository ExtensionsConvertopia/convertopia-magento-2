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

namespace Innovadeltech\Convertopia\Block\Rewrite\Search;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Search\Model\QueryFactory;

class Result extends \Magento\CatalogSearch\Block\Result
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
     * Result constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param LayerResolver $layerResolver
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param QueryFactory $queryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        LayerResolver $layerResolver,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        QueryFactory $queryFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        parent::__construct($context, $layerResolver, $catalogSearchData, $queryFactory, $data);
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_q = $this->request->getParam('q');
        return $this;
    }

    /**
     * Retrieve loaded product collection
     *
     * The goal of this method is to choose whether the existing collection should be returned
     * or a new one should be initialized.
     *
     * It is not just a caching logic, but also is a real logical check
     * because there are two ways how collection may be stored inside the block:
     *   - Product collection may be passed externally by 'setCollection' method
     *   - Product collection may be requested internally from the current Catalog Layer.
     *
     * And this method will return collection anyway,
     * even when it did not pass externally and therefore isn't cached yet
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getProductCollection()
    {
        if ($this->_q) {
            $collection = $this->collectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addFieldToFilter('entity_id', ['in' => $this->_q]);
            $this->_productCollection = $collection;
        }

        return $this->_productCollection;
    }

    /**
     * Get result count
     *
     * @return int
     */
    public function getResultCount()
    {
        if (!$this->hasData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->_getQuery()->saveNumResults($size);
            $this->setData('result_count', $size);
        }

        return $this->getData('result_count');
    }
}
