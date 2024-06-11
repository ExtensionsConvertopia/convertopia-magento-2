<?php

namespace Innovadeltech\Convertopia\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success
{

    /**
     * @var \Magento\Framework\App\Http\Context
     */

    protected $helper;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */

    protected $jsonHelper;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Innovadeltech\Convertopia\Helper\PackageDelay $helper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Innovadeltech\Convertopia\Helper\PackageDelay $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->helper = $helper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext);
    }

    /**
     * This returns Grand Total Value
     *
     * @return int
     */
    public function getGrandTotal()
    {
        /**
         * Here Order class is called to use its function
         *
         * @var \Magento\Sales\Model\Order $order */
        $order = $this->_checkoutSession->getLastRealOrder();
        return $order->getGrandTotal();
    }
    /**
     * This returns js encoded result value
     *
     * @return Array
     */
    public function getDummy1()
    {
        $shipping_arr = [];

        $order = $this->_checkoutSession->getLastRealOrder();
        // if (!is_null($order->getCustomerId())) {
        if (!($order->getCustomerId() === null)) {
            $customerId = $order->getCustomerId(); // get customer ID
            $customerEmail = $order->getCustomerEmail(); // get customer email
        } else {

            $customerEmail = $order->getCustomerEmail();
            $customerId = 'guest';
        }
        $encodedString = base64_encode($customerEmail); // encode the string in base64 format
        $shipping_arr['event'] = 'checkout_payment';
        $shipping_arr['line_items'] = $this->helper->getLineItems($order);
        $shipping_arr['user_email_hashed'] = $encodedString;
        $shipping_arr['user_billing_address'] = $this->helper->getBillingAddress($order);
        $shipping_arr['user_id'] = $customerId;
        $result = $this->jsonHelper->jsonEncode($shipping_arr);
        return $result;
    }
    /**
     * This returns js encoded result value
     *
     * @return Array
     */
    public function getDummy()
    {
        $shipping_arr = [];

        $order = $this->_checkoutSession->getLastRealOrder();
        if (!($order->getCustomerId() === null)) {
            $customerId = $order->getCustomerId(); // get customer ID
            $customerEmail = $order->getCustomerEmail(); // get customer email
        } else {
            $customerEmail = "";
            $customerId = 'guest';
        }
        $encodedString = base64_encode($customerEmail); // encode the string in base64 format
        $shipping_arr['event'] = 'order_confirmation';
        $shipping_arr['line_items'] = $this->helper->getLineItems($order);
        $shipping_arr['user_email_hashed'] = $encodedString;
        $shipping_arr['user_billing_address'] = $this->helper->getBillingAddress($order);
        $shipping_arr['user_shipping_address'] = $this->helper->getShippingAddress($order);
        $shipping_arr['order_total'] = $order->getGrandTotal();
        $shipping_arr['currency_code'] = $order->getData('order_currency_code');
        $shipping_arr['user_id'] = $customerId;

        $result = $this->jsonHelper->jsonEncode($shipping_arr);
        return $result;
    }
}
