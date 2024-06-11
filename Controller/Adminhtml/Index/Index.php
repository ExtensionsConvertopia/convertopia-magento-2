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
namespace Innovadeltech\Convertopia\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{

    public const ADMIN_RESOURCE = 'Innovadeltech_Convertopia::innovadeltech_convertopia_admin_menu';

    /**
     * @var resultPageFactory
     */
    protected $resultPageFactory;
    /**
     * Get construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Get execute
     *
     * @var execute
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
