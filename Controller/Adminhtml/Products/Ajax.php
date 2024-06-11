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

namespace Innovadeltech\Convertopia\Controller\Adminhtml\Products;

use \Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Ajax extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    protected $exportProductFactory;

    protected $resultJsonFactory;


    /**
     *
     * @param Context $context
     * @param \Innovadeltech\Convertopia\Model\Products\ExportFactory $exportProductFactory
     */

    public function __construct(
        Context $context,
        \Innovadeltech\Convertopia\Model\Products\ExportFactory $exportProductFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->exportProductFactory = $exportProductFactory;
        $this->resultJsonFactory = $resultJsonFactory;

    }

    /**
     * Get execute
     *
     * @var execute
     */
    public function execute()
    { 
        $isDelta=false;
        $response=$this->exportProductFactory->create()->getExport($isDelta);

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['result' => $response]);

    }
}
