<?php

/**
 * ShopifyAuthRequest
 */
class ShopifyAuthRequest extends DataObject {

    private static $app_key = null;

    private static $shared_secret = null;

    private static $callback_url = null;

    private static $scope = null;

    private static $error_path = null;

    // State Token Lifetime in Seconds
    private static $DEFAULT_STATE_LIFETIME = 900;
    private static $DEFAULT_STATE_LIFETIME_BUFFER = 10; // 10 seconds

    public static function set_state_lifetime($lifetime){
        self::$DEFAULT_STATE_LIFETIME = $lifetime;
    }

    public static function get_state_lifetime(){
        return self::$DEFAULT_STATE_LIFETIME;
    }

    public static function get_calc_state_lifetime(){
        return (self::$DEFAULT_STATE_LIFETIME + self::$DEFAULT_STATE_LIFETIME_BUFFER);
    }

    private static $db = array(
        'StateToken' => 'Varchar(32)'
    );

    private static $indexes = array(
        'StateToken' => true
    );

    public static function State($Token = false){

        self::cleanup();

        if($Token){
            if(ShopifyAuthRequest::get()->filter(array("StateToken" => $Token))->First()) return true;
            else return false;
        }else{

            $o_StateToken = new ShopifyAuthRequest();

            do{
                $o_StateToken->StateToken = md5(microtime().base64_encode(pack('N6', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), uniqid())));
            }while(ShopifyAuthRequest::State($o_StateToken->StateToken));

            $o_StateToken->write();

            return $o_StateToken->StateToken;
        }
    }

    /**
     * Delete old states
     */
    private static function cleanup(){
        // delete all old state tokens
        foreach(ShopifyAuthRequest::get()->Where("Created < '".date ("Y-m-d H:i:s", strtotime("- ".self::get_calc_state_lifetime()." seconds"))."'") as $o_ST){
            $o_ST->delete();
        }
    }

}
