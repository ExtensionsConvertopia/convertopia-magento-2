<?php

namespace Innovadeltech\Convertopia\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use \Innovadeltech\Convertopia\Model\Products\ProductTilesShowModel;

class Products extends Action
{
    protected $jsonFactory;
    protected $productFactory;
    protected $CustomBlock;
    protected $productRepository;
    protected $productTilesShowModel;
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        ProductTilesShowModel $productTilesShowModel,
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->productTilesShowModel = $productTilesShowModel;
        parent::__construct($context);
    }

    public function execute()
    {

        // $url = 'http://52.51.49.68/?user_id=1'; // Replace with your URL

        // $ch = curl_init($url);

        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $response = curl_exec($ch);

        // if ($response === false) {
        //     echo 'Error: ' . curl_error($ch);
        //     // Handle the error
        // } else {
        //     $data = json_decode($response, true);

        //     // if ($data === null) {
        //     //     echo 'Error decoding JSON: ' . json_last_error_msg();
        //     //     // Handle JSON decoding error
        //     // } 
        //     // else {
        //     //     // Now $data contains the JSON array
        //     //     print_r($data);
        //     // }
        // }

        // curl_close($ch);

        $formKey = $this->getRequest()->getParam('form_key');
        $productIds = $this->getRequest()->getParam('product_ids');
        // $productIdsArray = explode(',', $productIds);

        // $productsHtml= $this->CustomBlock->setProductIds($productIdsArray)->toHtml();
        $productsInfo = [];

        // ===============================it still have issues

        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $ProductTilesShowModel = $objectManager->get('\Innovadeltech\Convertopia\Model\Products\ProductTilesShowModel');
        $ProductTilesShowHtml = $this->productTilesShowModel->getProductTilesHTML($formKey);
        // $ProductTilesShowHtml = $ProductTilesShowModel->getProductTilesHTML($data["recommended"]);
        // // $productIdsArray = explode(',', $productIds);

        // // Load the product collection based on the provided product IDs.
        // $productCollection =  $objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        // $productCollection->addAttributeToSelect('*');
        // $productCollection->addIdFilter($productIdsArray);
        // $ScopeConfig = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        // $product_list_heading = $ScopeConfig->getValue('convertopia/product_list/product_list_heading');
        // $product_tiles = $ScopeConfig->getValue('convertopia_recommendations/product_list/product_tiles');

        // $html = '<h1>' . $product_list_heading . '</h1>';
        // $html .= '<div class="recommend-lists">';

        // $count = 0;
        // foreach ($productCollection as $product) {
        //     $productParentId = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
        //     if (!empty($productParentId)) {
        //         $productParent = $objectManager->create('Magento\Catalog\Model\Product')->load($productParentId[0]);

        //         $productUrl =  $objectManager->get('Magento\Catalog\Model\Product\Url')->getUrl($productParent);
        //         $productName = $productParent->getName();
        //         $productImage =  $objectManager->get('Magento\Catalog\Helper\Image')->init($productParent, 'product_thumbnail_image')->resize(100, 100)->getUrl();
        //     } else {
        //         $productUrl =  $objectManager->get('Magento\Catalog\Model\Product\Url')->getUrl($product);
        //         $productName = $product->getName();
        //         $productImage =  $objectManager->get('Magento\Catalog\Helper\Image')->init($product, 'product_thumbnail_image')->resize(100, 100)->getUrl();
        //     }
        //     $count++;

        //     // Generate HTML for each product.
        //     $html .= '<div class="recommend-list">';
        //     $html .= '<div class="recommend-inner">';
        //     $html .= '<a href="' . $productUrl . '"><img src="' . $productImage . '" alt="' . $productName . '" /></a>';
        //     $html .= '<h2><a href="' . $productUrl . '">' . $productName . '</a></h2>';
        //     $html .= '</div>';
        //     $html .= '</div>';

        //     if ($count == $product_tiles) {
        //         break;
        //     }
        // }

        // $html .= '</div>';
        $resultJson = $this->jsonFactory->create();
        return $resultJson->setData(['success' => true, 'products_info' =>  $ProductTilesShowHtml]);
        // return $resultJson->setData(['success' => true, 'products_info' =>  "<div>coming from controller</div>"]);
        // return $resultJson->setData(['success' => true]);
    }
}
