<?php

/**
 * @category   Innovadel Technologies Ltd.
 * @package    Innovadeltech_Convertopia
 * @subpackage etc
 * @author     Innovadel Technologies <support@innovadeltech.com>
 * @copyright  Copyright (c) 2019 Innovadel Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 */

namespace Innovadeltech\Convertopia\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\RequestInterface;

class Main extends Template
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
     * Main constructor.
     *
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        parent::__construct($context, $data);
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
     * Get search collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getSearchCollection()
    {
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('entity_id', ['in' => $this->_q]);
//        $collection->setPageSize(3); // fetching only 3 products
        return $collection;
    }
}
