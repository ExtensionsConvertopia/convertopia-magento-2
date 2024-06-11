<?php

namespace Innovadeltech\Convertopia\Block\System\Config;

class DeleteEmpty extends \Magento\Config\Block\System\Config\Form\Field
{


     /**
      *
      * @var \Magento\Backend\Helper\Data
      */
      protected $_backendData;

    protected $_template = 'Innovadeltech_Convertopia::system/config/delete_empty.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        array $data = []
    ) {
        $this->_backendData = $backendData;

        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }


    /**
     * It will return the Admin URi
     *
     * @return string
     */
    public function getAdminUri()
    {
        return $this->_backendData->getAreaFrontName();
    }
    public function getProductFeedUrl()
    {
        $feedUrls=[];
        $feedUrls['convertopia-products-ajax']=$this->getUrl('convertopia/products/ajax');
        $feedUrls['convertopia-products-delta']=$this->getUrl('convertopia/products/delta');
        $feedUrls['convertopia-customers-ajax']=$this->getUrl('convertopia/customers/ajax');
        $feedUrls['convertopia-customers-delta']=$this->getUrl('convertopia/customers/delta');
        $feedUrls['convertopia-orders-ajax']=$this->getUrl('convertopia/orders/ajax');
        $feedUrls['convertopia-orders-delta']=$this->getUrl('convertopia/orders/delta');
        return $feedUrls;
    }
    public function getProductFeedButton()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'class' => 'convertopia-feed',
                'label' => __('Generate Products Feed'),
            ]
        );
        return $button->toHtml();
    }

    public function getProductDeltaFeedButton()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'class' => 'convertopia-feed',
                'label' => __('Generate Delta Products Feed'),
            ]
        );

        return $button->toHtml();
    }
  
    

    public function getCustomerFeedButton()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'class' => 'convertopia-feed',
                'label' => __('Generate Customer Feed'),
            ]
        );
        return $button->toHtml();
    }

    public function getCustomerDeltaFeedButton()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'class' => 'convertopia-feed',
                'label' => __('Generate Delta Customer Feed'),
            ]
        );

        return $button->toHtml();
    }


    public function getOrderFeedButton()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'class' => 'convertopia-feed',
                'label' => __('Generate Order Feed'),
            ]
        );
        return $button->toHtml();
    }

    public function getOrderDeltaFeedButton()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'class' => 'convertopia-feed',
                'label' => __('Generate Delta Order Feed'),
            ]
        );

        return $button->toHtml();
    }
     
    
}
