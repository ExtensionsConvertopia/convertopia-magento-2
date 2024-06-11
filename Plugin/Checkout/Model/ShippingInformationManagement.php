<?php

namespace Innovadeltech\Convertopia\Plugin\Checkout\Model;

class ShippingInformationManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var \Innovadeltech\Convertopia\Helper\PackageDelay
     */
    protected $helper;
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;
    /**
     * This is the contructor For class PackageDelay
     *
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \Innovadeltech\Convertopia\Helper\PackageDelay $helper
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Innovadeltech\Convertopia\Helper\PackageDelay $helper,
        \Magento\Checkout\Helper\Cart $cartHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $_checkoutSession;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
    }
    /**
     * This function return beforeSaveAddressInformation value
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param int $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $emailAddress = $this->cartHelper->getQuote()->getCustomerEmail();

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Payment_step.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('text message');
        $data_new = [
            'event' => 'checkout_shipping',
            'line_items' => $this->helper->lineItems(),
            'user_shipping_address' => $this->getShippingDetails($addressInformation),
            'user_email_hashed' => base64_encode($this->helper->getHashedEmailString()),
            'user_id' => $this->helper->getLoggedinCustomerName()
        ];
        /**
         * $data_new = [
         *  'event' => 'checkout_shipping',
         *  'line_items' => $this->helper->lineItems(),
         *  'user_shipping_address' => $this->getShippingDetails($addressInformation),
         *  'user_email_hashed' => $this->helper->getCustomerHashedEmail(),
         *  'user_id' => $this->helper->getLoggedinCustomerName()
         *  ];
         *  $data_new['new']="welcome";
         *  $deliveryDate = $extAttributes->getDeliveryDate();
         */
        $this->_checkoutSession->setValueCheckoutShippingData($data_new);
    }
    /**
     * This function return getShippingDetails value
     *
     * @param object $addressInformation
     */
    public function getShippingDetails($addressInformation)
    {
        $shipping_arr = [];
        $shippingAddres = $addressInformation->getShippingAddress();
        $shipping_arr['first_name'] = $shippingAddres->getData('firstname');
        $shipping_arr['last_name'] = $shippingAddres->getData('lastname');
        $shipping_arr['address1'] = $shippingAddres->getData('street');
        $shipping_arr['city'] = $shippingAddres->getData('city');
        $shipping_arr['state'] = $shippingAddres->getData('region');
        $shipping_arr['zip'] = $shippingAddres->getData('postcode');
        $shipping_arr['country'] = $shippingAddres->getData('country_id');
        /**
         * $shipping_arr['email'] = $shippingAddres->getData('email');
         * $shippingMethod = $shippingAddress->getShippingMethod();
         * return $main_shipping_arr;
         */
        return ($shipping_arr);
    }
}
