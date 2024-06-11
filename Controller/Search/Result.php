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

namespace Innovadeltech\Convertopia\Controller\Search;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;

class Result extends \Magento\CatalogSearch\Controller\Result\Index
{
    /**
     * @var Session
     */
    protected $_catalogSession;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * Result constructor.
     *
     * @param Context $context
     * @param Session $catalogSession
     * @param StoreManagerInterface $storeManager
     * @param QueryFactory $queryFactory
     * @param Resolver $layerResolver
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        Context $context,
        Session $catalogSession,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        RedirectFactory $redirectFactory
    ) {
        parent::__construct($context, $catalogSession, $storeManager, $queryFactory, $layerResolver);
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * Execute action
     *
     * @return RedirectInterface|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $needle = $this->getRequest()->getParam('q');
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);

        /* @var $query \Magento\Search\Model\Query */
        $query = $this->_queryFactory->get();

        $storeId = $this->_storeManager->getStore()->getId();
        $query->setStoreId($storeId);

        $queryText = $query->getQueryText();
        if ($queryText != '') {
            $catalogSearchHelper = $this->_objectManager->get(\Magento\CatalogSearch\Helper\Data::class);
            $this->getNotCacheableResult($catalogSearchHelper, $query);
        } else {
            $resultRedirect = $this->redirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
    }

    /**
     * Get not cacheable result
     *
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchHelper
     * @param \Magento\Search\Model\Query $query
     */
    private function getNotCacheableResult($catalogSearchHelper, $query)
    {
        if ($catalogSearchHelper->isMinQueryLength()) {
            $query->setId(0)->setIsActive(1)->setIsProcessed(1);
        } else {
            $query->saveIncrementalPopularity();
            $redirect = $query->getRedirect();
            if ($redirect && $this->_url->getCurrentUrl() !== $redirect) {
                $resultRedirect = $this->redirectFactory->create();
                $resultRedirect->setUrl($redirect);
                return $resultRedirect;
            }
        }

        $catalogSearchHelper->checkNotes();

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
