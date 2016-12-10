<?php

Member::add_extension('ShopifyMember');

/**
 * ShopifyMember
 */

ShopifyMember::set_shopify_api_key(SHOPIFY_APP_API_KEY);
ShopifyMember::set_shopify_shared_key(SHOPIFY_APP_SHARED_SECRET);
ShopifyMember::set_shopify_callback_url(SHOPIFY_APP_CALLBACK_URL);
ShopifyMember::set_shopify_scope(SHOPIFY_APP_SCOPE);
ShopifyMember::set_error_path(SHOPIFY_ERROR_PATH);

// ShopifyMember::set_signup_path(SHOPIFY_SIGNUP_PATH);