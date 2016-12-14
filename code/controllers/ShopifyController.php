<?php
/**
* Implements a basic Controller
* @package some config
* http://doc.silverstripe.org/framework/en/3.1/topics/controller
*/
class ShopifyController extends Controller {
  
  private static $allowed_actions = array(
    'install',
    'signup',
    'auth',
    'error'
  );
  
  public function init() {
    parent::init();
  }
  
  public function install() {
    //   https://pepper.vagrantshare.com/shopify/install?shop=pepper-test-store.myshopify.com
    if (!isset($_GET['shop'])) {
      // missing shop parameter
      return $this->redirect(ShopifyMember::get_shopify_error_path());
    }
    if (!preg_match('/^[a-zA-Z0-9\-]+.myshopify.com$/', $_GET['shop'])) {
      // Invalid myshopify.com store URL.
      return $this->redirect(ShopifyMember::get_shopify_error_path());
    }
    $client = new \whatsbrand\shopifyapi\ShopifyClient($_GET['shop'], "", ShopifyMember::get_shopify_api_key(), ShopifyMember::get_shopify_shared_key());
    return $this->redirect($client->getInstallUrl());
  }
  
  public function auth() {
    if (!isset($_GET['shop'])) {
      // missing shop parameter
      return $this->redirect(ShopifyMember::get_shopify_error_path());
    }
    if($o_Member = Member::get()->filter(array('Shop' => $_GET['shop']))->First()){
      // Shopify member found
      // login and redirect
      $o_Member->logIn();
      return $this->redirect(Security::config()->default_login_dest);
    } else {
      $client = new \whatsbrand\shopifyapi\ShopifyClient($_GET['shop'], "", ShopifyMember::get_shopify_api_key(), ShopifyMember::get_shopify_shared_key());
      // TODO: Should be transfered to shopifyclient ---->
      if (isset($_GET['url']))
        unset($_GET['url']);
      // <-----
      if (!$client->validateSignature($_GET)) {
        return $this->redirect(ShopifyMember::get_shopify_error_path());
      }
      // TODO: Could be a login action
      if (!isset($_GET['code']))
      {
        // example scope for full access: 'read_content,write_content,read_themes,write_themes,read_products,write_products,read_customers,write_customers,read_orders,write_orders,read_script_tags,write_script_tags,read_fulfillments,write_fulfillments,read_shipping,write_shipping'
        $permission_url = $client->getAuthorizeUrl(ShopifyMember::get_shopify_scope(), ShopifyMember::get_shopify_callback_url());
        return $this->redirect($permission_url);
      }
      try
      {
        if (isset($_GET['code'])) {
          $oauth_token = $client->getAccessToken($_GET['code']);
          
          Session::set('oauth_token', $oauth_token);
          Session::set('shop', $_GET['shop']);
          
          $o_Member = new Member();
          
          $o_Member->AccessToken = $oauth_token;
          
          $o_Member->Shop = $_GET['shop'];
          
          $o_Member->Email = "info@".$_GET['shop']; 
          
          $o_Member->logIn();
          
          return $this->redirect(Security::config()->default_login_dest);
        }
      }
      catch (ShopifyApiException $e)
      {
        # HTTP status code was >= 400 or response contained the key 'errors'
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
      }
      catch (ShopifyCurlException $e)
      {
        # cURL error
        echo $e;
        print_R($e->getRequest());
        print_R($e->getResponse());
      }
    // }
  }
  
  public function signup() {
    var_dump($_GET);
    $client = new \whatsbrand\shopifyapi\ShopifyClient($_GET['shop'], "", ShopifyMember::get_shopify_api_key(), ShopifyMember::get_shopify_shared_key());
    
    if (!$client->validateSignature($_GET)) {
      return $this->redirect(ShopifyMember::get_shopify_error_path());
    }
    
    // TODO: Could be a login action
    if (!isset($_GET['code']))
    {
      // example scope for full access: 'read_content,write_content,read_themes,write_themes,read_products,write_products,read_customers,write_customers,read_orders,write_orders,read_script_tags,write_script_tags,read_fulfillments,write_fulfillments,read_shipping,write_shipping'
      $permission_url = $client->getAuthorizeUrl(ShopifyMember::get_shopify_scope(), ShopifyMember::get_shopify_callback_url());
      return $this->redirect($permission_url);
    }
    try
    {
      if (isset($_GET['code'])) {
        $oauth_token = $client->getAccessToken($_GET['code']);
        
        // Session::set('oauth_token', $oauth_token);
        // Session::set('shop', $_GET['shop']);
        
        $o_Member = new Member();
        
        $o_Member->AccessToken = $oauth_token;
        
        $o_Member->Shop = $_GET['shop'];
        
        $o_Member->Email = "info@".$_GET['shop']; 
        
        var_dump($o_Member);
      }
    }
    catch (ShopifyApiException $e)
    {
      # HTTP status code was >= 400 or response contained the key 'errors'
      echo $e;
      print_R($e->getRequest());
      print_R($e->getResponse());
    }
    catch (ShopifyCurlException $e)
    {
      # cURL error
      echo $e;
      print_R($e->getRequest());
      print_R($e->getResponse());
    }
  } 
  
  public function error() {
    // Print error
  }
}
