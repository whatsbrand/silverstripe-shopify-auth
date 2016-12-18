<?php
/**
* Implements a basic Controller
* @package some config
* http://doc.silverstripe.org/framework/en/3.1/topics/controller
*/
class ShopifyController extends Controller {
  
  public static $url_topic = 'shopify';
  
  public static $url_segment = 'shopify';
  
  private static $allowed_actions = array(
    'install',
    'auth',
    'error'
  );
  
  public static $template = 'BlankPage';
  
  /**
   * Template thats used to render the pages.
   *
   * @var string
   */
  public static $template_main = 'Page';

  /**
   * Returns a link to this controller.  Overload with your own Link rules if they exist.
   */
  public function Link() {
    return self::$url_segment .'/';
  }
  
  public function init() {
    parent::init();
  }
  
  public function install() {
    
    if(Member::currentUser()) return $this->redirect(Security::config()->default_login_dest);
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
    
    if(Member::currentUser()) return $this->redirect(Security::config()->default_login_dest);
    
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
      // TODO: Should be transferred to shopifyclient ---->
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
          
          // Session::set('oauth_token', $oauth_token);
          // Session::set('shop', $_GET['shop']);
          
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
    }
  }
  
  public function error() {
    return $this->customise(new ArrayData(array(
        'Title' => 'Shopify error title',
        'Content' => 'Shopify error content'
    )))->renderWith(
        array('Shopify_error', 'Shopify', $this->stat('template_main'), $this->stat('template'))
    );
  }
}
