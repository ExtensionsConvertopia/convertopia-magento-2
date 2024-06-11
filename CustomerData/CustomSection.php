<?php

namespace Innovadeltech\Convertopia\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class CustomSection implements SectionSourceInterface
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Innovadeltech\Convertopia\Helper\PackageDelay $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Innovadeltech\Convertopia\Helper\PackageDelay $helper,
        array $data = []
    ) {
        $this->_checkoutSession = $_checkoutSession;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
    }
    /**
     * Returns Array value
     */
    public function getSectionData()
    {
        $sss = [];

        $page_info = $this->helper->getRequestShow();

        $sss = $this->_checkoutSession->getValueAddToCartData();
        $qwer = $this->_checkoutSession->getValueCheckoutShippingData();
        if ($this->_checkoutSession->getValueAddToCartData()) {
            $sss['add_to_cart'] = $this->_checkoutSession->getValueAddToCartData();
        }
        if ($this->_checkoutSession->getValueCheckoutShippingData()) {
            $sss['checkout_shipping'] = $this->_checkoutSession->getValueCheckoutShippingData();
        }
        $result = $this->jsonHelper->jsonEncode($sss);
        return [
            'customdata' => $result
        ];
    }
}
