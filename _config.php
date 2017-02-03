<?php

Member::add_extension('ShopifyMember');

Config::inst()->update('ShopifyAuthRequest', 'app_key', SHOPIFY_APP_API_KEY);
Config::inst()->update('ShopifyAuthRequest', 'shared_secret', SHOPIFY_APP_SHARED_SECRET);
Config::inst()->update('ShopifyAuthRequest', 'callback_url', SHOPIFY_APP_CALLBACK_URL);
Config::inst()->update('ShopifyAuthRequest', 'scope', SHOPIFY_APP_SCOPE);
Config::inst()->update('ShopifyAuthRequest', 'error_path', SHOPIFY_ERROR_PATH);
