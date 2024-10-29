<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly 
}
/**
 * Class SFAIRP_SimpleFeedBotsProtectionAdmin
 *
 * This class represents the API to the Simplefeed Admin
 * For Admin API
 
 * @category Admin
 * @package  Admin
 * @author   alex <a.khomichenko@simplefeed.com>
 * @license  https://simplefeed.com SimpleFeed
 * @link     https://simplefeed.com
 * 
 * This class represents the API to the Simplefeed Admin
 */

class SFAIRP_SimpleFeedBotsProtectionAdmin
{
    
    // This service is used for managing bot protection settings, logging bot request statistics, and verifying premium access for the WordPress user. More info in the plugin's readme file.
    private static $_REST_URL = "https://www.simplefeed.com/wp-content/simplefeed/wp-simplefeed-bots-protection-admin-rest.php";    
    public function __construct() { }

    public static function getRestUrl() {
        $url = self::$_REST_URL;
        $url = $url."?d=".wp_parse_url(get_site_url())['host'];
        return $url;
    }


    public function updateSettings($body) {
        wp_remote_post(
            self::getRestUrl()."&action=settings",
            array(
                'timeout'=>10,
                'headers' => array('Accept' => 'application/json'),  
                'body'=>$body,
                'sslverify' => false
            )
        );
    }

    public function loadSettings() {
        $r = wp_remote_get(
            self::getRestUrl()."&action=settings",
            array(
                'timeout'=>10,
                'headers' => array('Accept' => 'application/json'),
                'sslverify' => false
            )
        );
        $tmp = wp_remote_retrieve_body( $r );
        if( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) || empty($tmp) ) {
            return null;
        }
        $r = json_decode( wp_remote_retrieve_body( $r ));
        return $r;
    }

    public function updateSitemap($body) {
        wp_remote_post(
            self::getRestUrl()."&action=sitemap",
            array(
                'timeout'=>10,
                'headers' => array('Accept' => 'application/json'),  
                'body'=>$body,
                'sslverify' => false
            )
        );
    }

}