<?php


namespace Innovadeltech\Convertopia\Model\Products;



class ProductTilesShowModel
{
    protected $Session;
    protected $UrlInterface;
    protected $ProductCollection;
    protected $ScopeConfigInterface;
    protected $ProductTypeConfigurable;
    protected $ModelProduct;
    protected $ModelProductUrl;
    protected $HelperImage;


    protected $blockFactory;


    public function __construct(
        \Magento\Customer\Model\Session $Session,
        \Magento\Framework\UrlInterface $UrlInterface,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $ProductCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $ScopeConfigInterface,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $ProductTypeConfigurable,
        \Magento\Catalog\Model\Product $ModelProduct,
        \Magento\Catalog\Model\Product\Url $ModelProductUrl,
        \Magento\Catalog\Helper\Image $HelperImage,


        \Magento\Framework\View\Element\BlockFactory $blockFactory

    ) {
        $this->Session = $Session;
        $this->UrlInterface = $UrlInterface;
        $this->ProductCollection = $ProductCollection;
        $this->ScopeConfigInterface = $ScopeConfigInterface;
        $this->ProductTypeConfigurable = $ProductTypeConfigurable;
        $this->ModelProduct = $ModelProduct;
        $this->ModelProductUrl = $ModelProductUrl;
        $this->HelperImage = $HelperImage;


        $this->blockFactory = $blockFactory;
    }


    public function getWidgetProductHtml()
    {

        // $block = $this->blockFactory->createBlock('Magento\CatalogWidget\Block\Product\ProductsList');

        // // $productsBlock = $this->getLayout()->createBlock(\Magento\CatalogWidget\Block\Product\ProductsList::class);
        // $block->setTitle("My products");
        // $block->setProductsCount(5);
        // // $block->setTemplate("product/widget/content/grid.phtml");
        // $block->setTemplate('Magento_CatalogWidget::product/widget/content/grid.phtml');
        // // $block->setConditionsEncoded("a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:1:`4`;]]");
        // $block->setConditions(
        //     [
        //         1 => [
        //             'type' => \Magento\CatalogWidget\Model\Rule\Condition\Combine::class,
        //             'aggregator' => 'all',
        //             'value' => '1',
        //             'new_child' => '',
        //         ],
        //         '1--1' => [
        //             'type' => \Magento\CatalogWidget\Model\Rule\Condition\Product::class,
        //             'attribute' => 'category_ids',
        //             'operator' => '==',
        //             'value' => '1234',
        //         ]
        //     ]
        // );
        // return $block->toHtml();



    }

    public function getTitle()
    {
        $product_list_heading = $this->ScopeConfigInterface->getValue('convertopia_recommendations/product_list/product_list_heading');
        return $product_list_heading;
    }
    public function canShowListing()
    {
        $enable_product_listing = $this->ScopeConfigInterface->getValue('convertopia_recommendations/product_list/enable_product_listing');
        return $enable_product_listing;
    }
    
    public function getProductSkus()
    {
        $customerId = $this->Session->getCustomer()->getId();
        if ($customerId == null) {
            $url = "http://52.51.49.68/?user_id=0";
        } else {
            $url = "http://52.51.49.68/?user_id=$customerId"; // Replace with your URL
        }
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
            // Handle the error
        } else {
            $data = json_decode($response, true);
        }

        curl_close($ch);

        $this->ProductCollection->addAttributeToSelect('*');



        // $staticIdArray = array(6,62,112,78,94,110);
        // $this->ProductCollection->addIdFilter($staticIdArray);
        $this->ProductCollection->addIdFilter($data["recommended"]);
        // $product_list_heading = $this->ScopeConfigInterface->getValue('convertopia/product_list/product_list_heading');
        $product_tiles = $this->ScopeConfigInterface->getValue('convertopia_recommendations/product_list/product_tiles');
        $skuArray = [];
        // $responseArray[] = '';
        // $responseArray['heading'] = $product_list_heading; 
        $count = 0;
        foreach ($this->ProductCollection as $product) {
            $productParentId = $this->ProductTypeConfigurable->getParentIdsByChild($product->getId());
            if (!empty($productParentId)) {

                $productParent = $this->ModelProduct->load($productParentId[0]);
                $parentSku = $productParent->getSku();
                $skuArray[] = $parentSku;
            } else {
                $productSku = $product->getSku();
                $skuArray[] = $productSku;
            }

            $count++;

            if ($count == $product_tiles) {
                break;
            }
        }
        //  $responseArray['skus'] = $skuArray; 
        $product_tiles = $this->ScopeConfigInterface->getValue('convertopia_recommendations/product_list/product_tiles');

        //  $responseArray['title'] = $product_tiles; 
        $commaSeparatedString = implode(", ", $skuArray);
        return $commaSeparatedString;
    }


    public function getProductTilesHTML($formKeyString)
    {
        $customerId = $this->Session->getCustomer()->getId();
        if ($customerId == null) {
            $url = "http://52.51.49.68/?user_id=0";
        } else {
            $url = "http://52.51.49.68/?user_id=$customerId"; // Replace with your URL
        }
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
            // Handle the error
        } else {
            $data = json_decode($response, true);
        }

        curl_close($ch);

        $this->ProductCollection->addAttributeToSelect('*');
        $this->ProductCollection->addIdFilter($data["recommended"]);
        $product_list_heading = $this->ScopeConfigInterface->getValue('convertopia_recommendations/product_list/product_list_heading');
        $product_tiles = $this->ScopeConfigInterface->getValue('convertopia_recommendations/product_list/product_tiles');

        $html = '<h1>' . $product_list_heading . '</h1>';
        $html .= '<div class="recommend-lists">';

        $count = 0;
        foreach ($this->ProductCollection as $product) {
            $productParentId = $this->ProductTypeConfigurable->getParentIdsByChild($product->getId());
            if (!empty($productParentId)) {
                // $productParent = $objectManager->create('Magento\Catalog\Model\Product');
                $productParent = $this->ModelProduct->load($productParentId[0]);

                $productUrl =  $this->ModelProductUrl->getUrl($productParent);
                $productName = $productParent->getName();
                $productImage =  $this->HelperImage->init($productParent, 'product_thumbnail_image')->resize(100, 100)->getUrl();
            } else {
                $productUrl =  $this->ModelProductUrl->getUrl($product);
                $productName = $product->getName();
                $productImage =  $this->HelperImage->init($product, 'product_thumbnail_image')->resize(100, 100)->getUrl();
            }
            $count++;

            // Generate HTML for each product.
            $html .= '<div class="recommend-list">';
            $html .= '<div class="recommend-inner">';
            $html .= '<a href="' . $productUrl . '"><img src="' . $productImage . '" alt="' . $productName . '" /></a>';
            $html .= '<h2><a href="' . $productUrl . '">' . $productName . '</a></h2>';

            $html .= '<form id="product_addtocart_form" data-mage-init=\'{"catalogAddToCart":{}}\' action="' . $this->UrlInterface->getUrl('checkout/cart/add', ['product' => $product->getId()]) . '" method="post">';
            // $html .= $block->getBlockHtml('formkey');
            $html .= $formKeyString;
            $html .= '<input type="hidden" name="productid" id="productid" value="' . $product->getId() . '" />';
            $html .= '<input type="number" name="qty" id="qty" value="1" />'; // Input field for quantity
            $html .= '<button type="submit" title="Add to Cart" class="action tocart primary">';
            $html .= '<span>Add to Cart</span>';
            $html .= '</button>';
            $html .= '</form>';
            $html .= '</div>';
            $html .= '</div>';

            if ($count == $product_tiles) {
                break;
            }
        }

        $html .= '</div>';
        return $html;
    }
}
