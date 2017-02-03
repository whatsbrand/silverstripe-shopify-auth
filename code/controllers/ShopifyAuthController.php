<?php

/**
 * Implements a basic Controller
 * @package silverstripe-shopify-auth
 * http://doc.silverstripe.org/framework/en/3.5/topics/controller
 */
class ShopifyAuthController extends Controller {

  public static $url_topic = 'shopifyauth';

  public static $url_segment = 'shopifyauth';

  private static $allowed_actions = array(
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

  /**
   * Auth Steps:
   * 1. A Shopowner is logged in to his shop and clicks "GET" on the application within the Appstore
   * 2. https://__SHOP_URL__.myshopify.com/admin/api/auth?api_key=__YOUR_API_KEY__ is the URL, initiating the installation to the Shop
   * 3. Step 2 will redirect to https://__YOUR_APP_URL/__YOUR_AUTH_APTH__?hmac=__HMAC__&shop=__SHOP_URL__.myshopify.com&timestamp=__TIMESTAMP__
   * 4. if no "code" GET Var is set, app should respond with install URL https://__SHOP_URL__.myshopify.com/admin/oauth/authorize?client_id=__API_KEY__&scope=__SCOPE__&state=__STATE__&redirect_uri=__REDIRECT_URI__
   * 5. Shop Owner needs to confirm premissions and press install button.
   * 6. App is redirected to https://__YOUR_APP_URL/__YOUR_AUTH_APTH__?code=__CODE__&hmac=__HMAC__&shop=__SHOP_URL__.myshopify.com&state=__STATE__&timestamp=__TIMESTAMP__
   * 7. The "code" parameter needs to be exchanged to an access_token
   * 8. Step 3 to Step 7 are always replayed, each time the shop accesses the application
   */
  public function auth() {

    if(Member::currentUser()) return $this->redirect(Security::config()->default_login_dest);

    if (!isset($_GET['shop'])) {
      // missing shop parameter
      return $this->redirect(ShopifyAuthRequest::config()->error_path);
    }else{

      $client = new \whatsbrand\shopifyapi\ShopifyClient($_GET['shop'], "", ShopifyAuthRequest::config()->api_key, ShopifyAuthRequest::config()->shared_secret);

      // validate the signature
      if($client->validateSignature($_GET)){
        // If shop allready installed the application
        if($o_Member = Member::get()->filter(array('ShopifyShop' => $_GET['shop']))->First()){
          // check for still working access_token
          $shopify = new \whatsbrand\shopifyapi\ShopifyClient($o_Member->ShopifyShop, $o_Member->ShopifyAccessToken, ShopifyAuthRequest::config()->api_key, ShopifyAuthRequest::config()->shared_secret);

        	try
        	{
        		// Making an API request can throw an exception
        		$shop = $shopify->call('GET', '/admin/shop.json');
        		// compare returned ID with Member->ShopifyId
            var_dump($shop); die();
        	}
        	catch (\whatsbrand\shopifyapi\ShopifyApiException $e)
        	{
        		// HTTP status code was >= 400 or response contained the key 'errors'
            // TODO: Implement some logging here
        		return $this->redirect(ShopifyAuthRequest::config()->error_path);
        	}
        	catch (\whatsbrand\shopifyapi\ShopifyCurlException $e)
        	{
        		// cURL error
            // TODO: Implement some logging here
        		return $this->redirect(ShopifyAuthRequest::config()->error_path);
        	}
        }else{
          // installation steps
          if (!isset($_GET['code'])){

        		$permission_url = $client->getAuthorizeUrl(ShopifyAuthRequest::config()->scope, ShopifyAuthRequest::config()->callback_url, ShopifyAuthRequest::State());

            //die("<script type='text/javascript'> window.location.href='$permission_url'</script>");
            return $this->redirect($permission_url);

        	}else if(isset($_GET['code']) && isset($_GET['state']) && ShopifyAuthRequest::State($_GET['state'])){
            // permissions were granted and state was correct
            // start the exchange for an access_token and save the user
            $oauth_token = $client->getAccessToken($_GET['code']);

            // fetch basic information from shop
            $shopify = new \whatsbrand\shopifyapi\ShopifyClient($_GET['shop'], $oauth_token, ShopifyAuthRequest::config()->api_key, ShopifyAuthRequest::config()->shared_secret);

            try
            {
              // Making an API request can throw an exception
              $shop = $shopify->call('GET', '/admin/shop.json');

              if(isset($shop['id'])){
                $o_Member = Member::create();

                $o_Member->ShopifyAccessToken = $oauth_token;

                $o_Member->ShopifyShop = $_GET['shop'];

                $o_Member->ShopifyId = $shop['id'];

                $o_Member->Email = "info@".$_GET['shop'];

                $o_Member->logIn();

                // TODO: implement post installation steps
                return $this->redirect(Security::config()->default_login_dest);

              }else{
                return $this->redirect(ShopifyAuthRequest::config()->error_path);
              }
            }
            catch (\whatsbrand\shopifyapi\ShopifyApiException $e)
            {
              // HTTP status code was >= 400 or response contained the key 'errors'
              // TODO: Implement some logging here
              return $this->redirect(ShopifyAuthRequest::config()->error_path);
            }
            catch (\whatsbrand\shopifyapi\ShopifyCurlException $e)
            {
              // cURL error
              // TODO: Implement some logging here
              return $this->redirect(ShopifyAuthRequest::config()->error_path);
            }
          }else{
            return $this->redirect(ShopifyAuthRequest::config()->error_path);
          }
        }
      }else{
        return $this->redirect(ShopifyAuthRequest::config()->error_path);
      }
    }
  }

	/**
	 * Show the error page
	 */
	public function error() {

        return $this->customise(new ArrayData(array(
            'Title' => _t('ShopifyAuth.ERRORTITLE', 'ShopifyAuth.ERRORTITLE'),
            'Content' => _t('ShopifyAuth.ERRORCONTENT', 'ShopifyAuth.ERRORCONTENT')
        )))->renderWith(
            array('ShopifyAuthController_error', 'ShopifyAuthController', $this->stat('template_main'), $this->stat('template'))
        );
	}
}
