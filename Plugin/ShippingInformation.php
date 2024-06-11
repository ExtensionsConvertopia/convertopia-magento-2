<?php

namespace Innovadeltech\Convertopia\Plugin;

class ShippingInformation
{
    /**
     * @var \Innovadeltech\Convertopia\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Ths function returns aroundSaveAddressInformation value
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        $addressInformation
    ) {
        $result = $proceed($cartId, $addressInformation);
        /**
         * if (!$this->helper->isEnabled()) {
         *    return $result;
         * }
         */
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shpping_step.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('text message');
        /**
         * @var \Magento\Quote\Model\Quote $quote
         * $quote = $this->quoteRepository->getActive($cartId);
         * $shippingDescription = $quote->getShippingAddress()->getShippingDescription();
         * $this->_checkoutSession->setCheckoutOptionsData($this
         * ->helper->addCheckoutStepPushData('2', $shippingDescription));
         */
        return $result;
    }
}
