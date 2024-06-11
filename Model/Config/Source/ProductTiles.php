<?php

namespace Innovadeltech\Convertopia\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ProductTiles implements ArrayInterface
{
     /**
     * @var $storeManager
     */
    protected $storeManager;
    /**
     * @var $categoryCollection
     */
    protected $categoryCollection;

    /**
     * Categorylist constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager 
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory  $categoryCollection
     */

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
    ) {
        $this->_storeManager = $storeManager;
        $this->_categoryCollection = $categoryCollection;
    }


    /*  
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('1')],
            ['value' => 2, 'label' => __('2')],
            ['value' => 3, 'label' => __('3')],
            ['value' => 4, 'label' => __('4')],
            ['value' => 5, 'label' => __('5')],
            ['value' => 6, 'label' => __('6')],
            ['value' => 7, 'label' => __('7')],
            ['value' => 8, 'label' => __('8')],

        ];
    }

}
