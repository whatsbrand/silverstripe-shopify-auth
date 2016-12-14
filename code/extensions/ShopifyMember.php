<?php

class ShopifyMember extends DataExtension {
  
  private static $shopify_api_key = null;

  private static $shopify_shared_key = null;

  private static $shopify_callback_url = null;
  
  private static $shopify_scope = null;
  
  private static $shopify_signup_path = null;
  
  private static $shopify_error_path = null;
  
  public static function set_shopify_api_key($key){
    self::$shopify_api_key = $key;
  }
  
  public static function get_shopify_api_key(){
    return self::$shopify_api_key;
  }
  
  public static function set_shopify_shared_key($secret){
    self::$shopify_shared_key = $secret;
  }
  
  public static function get_shopify_shared_key(){
    return self::$shopify_shared_key;
  }
  
  public static function set_shopify_callback_url($url){
    self::$shopify_callback_url = $url;
  }
  
  public static function get_shopify_callback_url(){
    return self::$shopify_callback_url;
  }
  
  public static function set_shopify_scope($scope){
    self::$shopify_scope = $scope;
  }
  
  public static function get_shopify_scope(){
    return self::$shopify_scope;
  }
  
  public static function set_shopify_signup_path($path){
    self::$shopify_signup_path = $path;
  }
  
  public static function get_shopify_signup_path(){
    return self::$shopify_signup_path;
  }
  
  public static function set_shopify_error_path($path){
    self::$shopify_error_path = $path;
  }
  
  public static function get_shopify_error_path(){
    return self::$shopify_error_path;
  }
  
  private static $db = array(
    "Email" => "Varchar(255)",
    "AccessToken" => "Varchar(255)",
    "Shop" => "Varchar(255)"
  );
  
  private static $indexes = array(
    "Shop" => true
  );

}