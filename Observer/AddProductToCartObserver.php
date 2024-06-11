<?php

namespace Innovadeltech\Convertopia\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddProductToCartObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \WeltPixel\GoogleTagManager\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Innovadeltech\Convertopia\Helper\PackageDelay $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_checkoutSession = $_checkoutSession;
        $this->helper = $helper;
    }

    /**
     * This Function execue the observer for AddProductToCartObserver
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shpping_step.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('text message');

        $data_new = [];

        $product = $observer->getData('product');

        $data_new = [
            'event' => 'add_to_cart',
            'line_items' => $this->helper->lineItems(),
            'user_id' => $this->helper->getLoggedinCustomerName()
        ];
        /** $data_new['new']="welcome"; */
        $this->_checkoutSession->setValueAddToCartData($data_new);

        return $this;
    }
}
