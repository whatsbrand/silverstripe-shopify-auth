<?php

class ShopifyMember extends DataExtension {

  private static $db = array(
    "Email" => "Varchar(255)",
    "ShopifyAccessToken" => "Varchar(255)",
    "ShopifyId" => "Varchar(255)",
    "ShopifyShop" => "Varchar(255)"
  );

  private static $indexes = array(
    "ShopifyId" => true,
    "ShopifyShop" => true
  );

}
