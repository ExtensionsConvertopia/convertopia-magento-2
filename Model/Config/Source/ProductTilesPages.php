<?php

namespace Innovadeltech\Convertopia\Model\Config\Source;

class ProductTilesPages implements \Magento\Framework\Option\ArrayInterface
{
        public function toOptionArray()
        {
                return [
                        ['value' => 'catalog_product_view', 'label' => __('Product Page')],
                        ['value' => 'catalog_category_view', 'label' => __('Category Page')],
                        ['value' => 'cms_index_index', 'label' => __('Homepage')],
                        ['value' => 'customer_acount_index', 'label' => __('Account Info')],
                        ['value' => 'customer_address_billing', 'label' => __('Billing Info')],
                        ['value' => 'customer_address_shipping', 'label' => __('Delivery Info')],
                        ['value' => 'sales_order_history', 'label' => __('Recent Orders')],
                        ['value' => 'customer_acount_login', 'label' => __('Login')],
                        ['value' => 'customer_acount_create', 'label' => __('Sign Up')],
                        ['value' => 'contact_index_index', 'label' => __('Contact Us')],
                        ['value' => 'checkout_cart_index', 'label' => __('Cart Page')],
                        ['value' => 'checkout_onepage_success', 'label' => __('Order Success  Page')],
                        ['value' => 'cms_noroute_index', 'label' => __('404 Page')]
                ];
        }
}


