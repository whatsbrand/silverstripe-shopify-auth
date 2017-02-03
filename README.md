# silverstripe-shopify-auth

shopify application oauth module

## Maintainers

 * Andre Lohmann (Nickname: andrelohmann)
  <lohmann dot andre at googlemail dot com>
 * Eduard Malyj https://github.com/eduardma

## Requirements

Silverstripe 3.5.x

## Introduction

Using the OAuth mechanism, to authenticate shopify stores against silverstripe based shopify apps

## Preparations

### Developer Center

Login to https://developers.shopify.com/

### Shopify App

create an application on your shopify partner account with the following setting:

#### App Information

App name: your app name

#### SDK settings

EMBEDDED APP SDK -> Enabled

SHOPIFY POS APP SDK -> Disabled

#### App URLs

choose a vagrant-share name for you testing app

App URL: https://__APP_URL__/shopifyauth/auth

Preferences URL: https://__APP_URL__/__PATH_TO_PREFERENCES__

Support URL: https://__APP_URL__/__PATH_TO_SUPPORT__

Redirect URL: https://__APP_URL__/shopifyauth/auth

Shop Admin Links, Shop POS Links and App Proxies are not required

### Silverstripe App

Define the following constants in your _ss_environment.php

```
define('SHOPIFY_APP_API_KEY', '{{ shopify_app_api_key }}');
define('SHOPIFY_APP_SHARED_SECRET', '{{ shopify_app_shared_secret }}');
define('SHOPIFY_APP_CALLBACK_URL', 'https://{{ shopify_app_domain }}/shopifyauth/auth');
define('SHOPIFY_APP_SCOPE', 'read_products,read_orders,write_orders');
define('SHOPIFY_ERROR_PATH', 'shopifyauth/error');
```

A full list of scopes can be fonut here
https://help.shopify.com/api/guides/authentication/oauth#scopes
