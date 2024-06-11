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

namespace Innovadeltech\Convertopia\Block\Adminhtml;

class Main extends \Magento\Backend\Block\Template
{

     /**
      *
      * @var \Magento\Backend\Helper\Data
      */
    protected $_backendData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        array $data = []
    ) {
        $this->_backendData = $backendData;
        parent::__construct($context, $data);
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
}
