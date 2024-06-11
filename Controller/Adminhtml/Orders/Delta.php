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

namespace Innovadeltech\Convertopia\Controller\Adminhtml\Orders;

use \Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;


class Delta extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    protected $exportOrderFactory;
    protected $resultJsonFactory;

    /**
     *
     * @param Context $context
     * @param \Innovadeltech\Convertopia\Model\Orders\ExportFactory $exportOrderFactory
     */
    public function __construct(
        Context $context,
        \Innovadeltech\Convertopia\Model\Orders\ExportFactory $exportOrderFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->exportOrderFactory = $exportOrderFactory;
        $this->resultJsonFactory = $resultJsonFactory;

    }

    /**
     * Get execute
     *
     * @var execute
     */
    public function execute()
    {
        $isDelta=true;
        $response= $this->exportOrderFactory->create()->getExport($isDelta);
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['result' => $response]);
    }
}
