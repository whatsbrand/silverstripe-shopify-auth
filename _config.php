<?php

Member::add_extension('ShopifyMember');

define('SHOPIFY_APP_API_KEY', '3f2b4ee5a07f500596e0573a9c139098');
define('SHOPIFY_APP_SHARED_SECRET', 'd0b88d619f1baa1456e267a2480d3cec');
define('SHOPIFY_APP_CALLBACK_URL', 'https://pepper.vagrantshare.com/shopify/auth');
define('SHOPIFY_APP_SCOPE', 'read_content,write_content,read_themes,write_themes,read_products,write_products,read_customers,write_customers,read_orders,write_orders,read_script_tags,write_script_tags,read_fulfillments,write_fulfillments,read_shipping,write_shipping');
define('SHOPIFY_SIGNUP_PATH', 'https://pepper.vagrantshare.com/shopify/signup');
define('SHOPIFY_ERROR_PATH', 'https://pepper.vagrantshare.com/shopify/error');

/**
 * ShopifyMember
 */

ShopifyMember::set_shopify_api_key(SHOPIFY_APP_API_KEY);
ShopifyMember::set_shopify_shared_key(SHOPIFY_APP_SHARED_SECRET);
ShopifyMember::set_shopify_callback_url(SHOPIFY_APP_CALLBACK_URL);
ShopifyMember::set_shopify_scope(SHOPIFY_APP_SCOPE);
ShopifyMember::set_shopify_signup_path(SHOPIFY_SIGNUP_PATH);
ShopifyMember::get_shopify_error_path(SHOPIFY_ERROR_PATH);