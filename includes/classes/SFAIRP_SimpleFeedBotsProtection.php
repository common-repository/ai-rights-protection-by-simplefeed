<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly 
}
require_once 'SFAIRP_SimpleFeedBotsProtectionDatabase.php';
/**
 * Class SFAIRP_SimpleFeedBotsProtection
 *
 * This class represents the work with robots.txt for a SimpleFeed.
 * For BotProtection

 * @category BotProtection
 * @package  BotProtection
 * @author   alex <a.khomichenko@simplefeed.com>
 * @license  https://simplefeed.com SimpleFeed
 * @link     https://simplefeed.com
 * 
 * This class represents the work with robots.txt for a SimpleFeed.
 */
class SFAIRP_SimpleFeedBotsProtection
{

    private static $_pluginFILE;
    private static $_pluginDIR;

    public static $BOTS_DEFAULT = array(
        'OpenAI' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('GPTBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly' /* always, hourly, daily, weekly, monthly, yearly, never */
            )
        ),
        'ChatGPT' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('ChatGPT-User'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Googlebot' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Googlebot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'GooglebotNews' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Googlebot-News'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'GooglebotImage' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Googlebot-Image'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'GooglebotVideo' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Googlebot-Video'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'StorebotGoogle' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Storebot-Google'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'GoogleInspectionTool' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Google-InspectionTool'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'GoogleOther' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('GoogleOther'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'GoogleExtended' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Google-Extended'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'AdsBotGoogleMobile' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('AdsBot-Google-Mobile'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'GoogleAdsBot' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('AdsBot-Google'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'MediapartnersGoogle' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Mediapartners-Google'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Bingbot' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Bingbot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'MSNBot' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('MSNBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'MSNBotMedia' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('MSNBot-Media'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'AdIdxBot' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('AdIdxBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'BingPreview' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('BingPreview'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Yahoo' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Slurp'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'DuckDuckGo' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('DuckDuckBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Baidu' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Baiduspider'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'BaiduRender' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Baiduspider'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Yandex' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Yandexbot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Applebot' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Applebot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Facebook' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('FacebookExternalHit'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Twitter' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Twitterbot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'LinkedIn' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('LinkedInBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'TechnicalSEO' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('TechnicalSEOdotCom'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'ScreamingFrog' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Screaming Frog SEO Spider'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Botify' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Botify'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'OnCrawl' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('OnCrawl'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'MozCampaignDiagnostics' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('rogerbot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'MozMozscapeFreshscape' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('DotBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Majestic' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('MJ12bot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Ahrefs' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('AhrefsBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Alexa' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('ia_archiver'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'CommonCrawl' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('CCBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Amazon' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Amazonbot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'MetaFacebook' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('FacebookBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Perplexity' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('PerplexityBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'YouBot' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('YouBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Anthropic' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('anthropic-ai'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'ClaudeAnthropic' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Claude-Web'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'ClaudeBotAnthropic' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('ClaudeBot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Cohere' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('cohere-ai'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Webz.io' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Omgilibot','omgili'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'Diffbot' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Diffbot'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'ByteDanceTikTok' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Bytespider'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        ),
        'ApplebotExtended' => array(
            'enable' => false,
            'ip_contains' => array(),
            'user_agent_contains' => array('Applebot-Extended'),
            'deny_text' => 'To access this site and hundreds of others, please contact ai@simplefeed.com',
            'sitemap' => array(
                'items'=> array(
                    'numberposts' => 10,
                    'types' => array(),'categories' => array(),'tags' => array()
                ),
                'changefreq' => 'weekly'
            )
        )
    );

    public function __construct() {}

    public static function getClientUserAgent() {
        return isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
    }

    public static function getClientIp() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR'])));
            $ipaddress = trim(end($ipList));
        } elseif (isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED']));
        } elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && !empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']));
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR']) && !empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_FORWARDED_FOR']));
        } elseif (isset($_SERVER['HTTP_FORWARDED']) && !empty($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_FORWARDED']));
        } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        } else {
            $ipaddress = 'UNKNOWN';
        }
    
        return $ipaddress;
    }

    public static function getClientBot($BOTS,$alsoAmongDisabled) {
        $ip = self::getClientIp();
        $user_agent = self::getClientUserAgent();
        $bot = null;
        foreach ($BOTS as $key => $settings) {
            if ($settings["enable"]==true || $alsoAmongDisabled==true) {
                foreach ($settings["ip_contains"] as $x) {
                    if ( stripos( $ip, $x )!==false ) {
                        $bot = $key;
                    }
                }
                foreach ($settings["user_agent_contains"] as $x) {
                    if ( stripos( $user_agent, $x )!==false ) {
                        $bot = $key;
                    }
                }
            }            
        }
        return $bot;
    }

    private static function _convertToCustomDigits($number) {
        $digits = array('0'=>'🯰','1'=>'🯱','2'=>'🯲','3'=>'🯳','4'=>'🯴','5'=>'🯵','6'=>'🯶','7'=>'🯷','8'=>'🯸','9'=>'🯹');
        return implode('', array_map(function ($digit) use ($digits) { 
            return $digits[$digit]; 
        }, str_split($number)));
    }

    private static function _getPostsFromDatabase($botSettings,$sm) {
        $postsForSitemapSelect = array(
            'orderby'     => 'modified',
            'order'       => 'DESC'
        );
        if ($botSettings['sitemap']['items']['numberposts']!=null) {
            $postsForSitemapSelect['numberposts'] = $botSettings['sitemap']['items']['numberposts'];
        }
        // filter by category
        if ($sm->enable==true && isset($botSettings['sitemap']['items']['categories']) && !empty($botSettings['sitemap']['items']['categories'])) {
            $categories = $botSettings['sitemap']['items']['categories'];
            $postsForSitemapSelect['category__in'] = $categories; // categories' slug
        }
        // filter by tag
        if ($sm->enable == true && isset($botSettings['sitemap']['items']['tags']) && !empty($botSettings['sitemap']['items']['tags'])) {
            $tags = $botSettings['sitemap']['items']['tags'];
            $postsForSitemapSelect['tag__in'] = $tags;
        }
        // filter by post types
        if ($sm->enable == true && isset($botSettings['sitemap']['items']['types']) && !empty($botSettings['sitemap']['items']['types'])) {
            $types = $botSettings['sitemap']['items']['types'];
            $postsForSitemapSelect['post_type'] = $types;
        }
        $postsForSitemap = get_posts($postsForSitemapSelect);
        return $postsForSitemap;
    }

    public static function buildRobotsTxtResponse() {  
        $token = get_query_var('simplefeed_token'); $preview = false;
        $sm = null;
        if ($token!='') {
            $sm = SFAIRP_SimpleFeedBotsProtectionDatabase::selectSitemapLastByToken($token);
            $preview = true;
        }
        $override = false;
        $BOTS = SFAIRP_SimpleFeedBotsProtectionSettings::getBots();
        $BOTSEnabled = array_filter($BOTS, function ($x) {
            return isset($x['enable']) && $x['enable'] === true;
        });
        $sitemapEnable = false;
        $bot = null;
        if ($sm!=null) {
            $bot = $sm->bot; 
        } else {
            $bot = self::getClientBot($BOTS,true);
        }        
        if ($bot!=null) {
            $botSettings = $BOTS[$bot];
            $sitemapEnable = (isset($botSettings['sitemap']['items']['types']) && sizeof($botSettings['sitemap']['items']['types'])>0) || (isset($botSettings['sitemap']['items']['categories']) && sizeof($botSettings['sitemap']['items']['categories'])>0) || (isset($botSettings['sitemap']['items']['tags']) && sizeof($botSettings['sitemap']['items']['tags'])>0);
            $ip = null;
            if ($sm!=null) {
                $ip = $sm->ip;
            } else {
                $ip = self::getClientIp();
            }
            if ($sm==null) {
                $sm = SFAIRP_SimpleFeedBotsProtectionDatabase::selectSitemapLastByIpAndBot($ip,$bot);
            } 
            if ($preview==false) {
                if ($sm==null) {
                    $token = bin2hex(random_bytes(32 / 2));
                    SFAIRP_SimpleFeedBotsProtectionDatabase::insertSitemap(
                        $ip, 
                        self::getClientUserAgent(),
                        $bot,
                        $token,
                        $botSettings['enable'],
                        $botSettings['enable']
                    );
                    $sm = SFAIRP_SimpleFeedBotsProtectionDatabase::selectSitemapLastByIpAndBot($ip,$bot);  
                    wp_schedule_single_event(time()+10,'simplefeed_boots_protection_sitemap_sync',array($sm));
                }
            }            
        }
        $override = $sitemapEnable==true;
        $responseHeader = "# AI Rights Protection by SimpleFeed. Begin\n";
        $response = "";
        $i = 0;
        if ($override==true) {
            $BOTSEnabled = array($sm->bot=>$BOTS[$sm->bot]);
        }
        foreach ($BOTSEnabled as $botName => $botSettings) {            
            $i++; $id = self::_convertToCustomDigits($i); 
            $allow = false;
            if ($sm!=null && $sm->bot==$botName && $sm->enable==false) {                
                $allow = true;
                $override = false;
            }
            $robots_txt_requests = $sm->robots_txt_requests+1;
            $robots_txt_requests_200 = $sm->robots_txt_requests_200;
            $robots_txt_requests_403 = $sm->robots_txt_requests_403;
            if ($allow==false) { 
                if ($sitemapEnable==true) {
                    $response .= "# {$botSettings['deny_text']}\n";
                    $response .= "User-agent: {$botSettings['user_agent_contains'][0]}\n";
                    $response .= "Disallow: /\n"; 
                    $postsForSitemap = self::_getPostsFromDatabase($botSettings,$sm);
                    foreach ($postsForSitemap as $post) {
                        setup_postdata($post);
                        $link = esc_url(wp_make_link_relative(get_permalink($post)));
                        $response .= "Allow: $link\n";
                    }
                    wp_reset_postdata();
                    //$response .= "Sitemap: ".home_url('').'/?simplefeed_sitemap='.$token."\n";
                    $robots_txt_requests_200 = $robots_txt_requests_200 + 1;

                } else {
                    $response .= "# ───────────────────────────────────────────────────────────────────────────────────────────────────────────────\n";            
                    $response .= "# {$id}. {$botSettings['deny_text']}\n";
                    $response .= "User-agent: {$botSettings['user_agent_contains'][0]}\n";
                    $response .= "Disallow: /\n";    
                    $robots_txt_requests_403 = $robots_txt_requests_403 + 1;           
                }              
            } else {
                $robots_txt_requests_200 = $robots_txt_requests_200 + 1;
            }
            if ($sm->bot==$botName && $preview==false) {
                SFAIRP_SimpleFeedBotsProtectionDatabase::updateSitemap(
                    $sm->id,
                    $sm->token,
                    self::getClientUserAgent(),
                    $ip,
                    $robots_txt_requests,
                    $robots_txt_requests_200,
                    $robots_txt_requests_403,
                    $sm->enable
                );
                wp_schedule_single_event(time()+10,'simplefeed_boots_protection_sitemap_sync',array($sm));
            }  
        }
        $responseFooter = "# AI Rights Protection by SimpleFeed. End\n";
        if ($response!="") {
            $response = $responseHeader.$response.$responseFooter;
        }
        return array($override,$response);
    }

    public static function renderSitemap() {
        if (get_query_var('simplefeed_sitemap')) { 
            $token = get_query_var('simplefeed_sitemap');
            $asAdmin = get_query_var('simplefeed_preview') !=null;
            $sm = SFAIRP_SimpleFeedBotsProtectionDatabase::selectSitemapLastByToken($token);
            if ($sm!=null) {                
                $BOTS = SFAIRP_SimpleFeedBotsProtectionSettings::getBots();
                $botSettings = $BOTS[$sm->bot];                
                if ($asAdmin==false)  {
                    SFAIRP_SimpleFeedBotsProtectionDatabase::updateSitemap($sm->id,$token,self::getClientUserAgent(),self::getClientIp(),$sm->robots_txt_requests,$sm->robots_txt_requests_200 + 1,$sm->robots_txt_requests_403,$sm->enable);
                    $sm = SFAIRP_SimpleFeedBotsProtectionDatabase::selectSitemapLastByToken($token);
                    wp_schedule_single_event(time()+10,'simplefeed_boots_protection_sitemap_sync',array($sm));
                }                    
                header('Content-Type: application/xml; charset=UTF-8');
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
                
                $postsForSitemap = self::_getPostsFromDatabase($botSettings,$sm);
                
                $changefreq = 'weekly';
                if (isset($botSettings['sitemap']) && isset($botSettings['sitemap']['changefreq']) && $botSettings['sitemap']['changefreq']!='' ) {
                    $changefreq = $botSettings['sitemap']['changefreq'];
                }
                
                foreach ($postsForSitemap as $post) {
                    setup_postdata($post);
                    $postdate = explode(" ", $post->post_modified);
                    echo '<url>';
                    echo '<loc>' . esc_url(get_permalink($post)) . '</loc>';
                    echo '<lastmod>' . esc_html($postdate[0]) . '</lastmod>';
                    echo '<changefreq>'.esc_html($changefreq).'</changefreq>';
                    echo '<priority>0.5</priority>';
                    echo '</url>';
                }
                wp_reset_postdata();
                echo '</urlset>';
                exit;                   
            }
        }
    }

    public static function register($pluginFILE,$pluginDIR) {
        self::$_pluginFILE = $pluginFILE;
        self::$_pluginDIR = $pluginDIR;
        SFAIRP_SimpleFeedBotsProtectionDatabase::register($pluginFILE);  
        add_action('robots_txt', function ( $robots ) {
            $additional = SFAIRP_SimpleFeedBotsProtectionSettings::getAdditionalRobotsTxtRows();                
            return $robots."\n".$additional;
        },10);      
        if (SFAIRP_SimpleFeedBotsProtectionSettings::isEnabled()) {
            add_action('template_redirect', array(__CLASS__, 'renderSitemap'),11);
            add_filter('query_vars', function ($vars) {                                
                $vars[] = 'simplefeed_sitemap';
                $vars[] = 'simplefeed_token';
                $vars[] = 'simplefeed_preview';                
                return $vars;
            });
            add_action('do_robotstxt', function () {
                ob_start();
            },1);
            add_action('robots_txt', function ( $robots ) {
                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                header('Cache-Control: post-check=0, pre-check=0', false);
                header('Pragma: no-cache');
                $output = ob_get_clean();
                list($override,$response) = self::buildRobotsTxtResponse();
                if ($override==true) {
                    echo "".esc_textarea($response)."\n";
                } else {
                    echo esc_textarea($output).esc_textarea($robots)."\n\n".esc_textarea($response)."\n";
                }                
            },9999,1);
        } else {
            //SFAIRP_SimpleFeedBotsProtectionDatabase::insertLog("register: bots protection disabled");
        }
        add_action( 'simplefeed_boots_protection_sitemap_sync', array(__CLASS__, 'syncAsync'), 10, 1);
        add_filter( 'plugin_action_links_' . plugin_basename($pluginFILE), function ($links) {
            $settings_link = '<a href="options-general.php?page=simplefeed-bots-protection-menu">Settings & Dashboard</a>';
            array_unshift($links, $settings_link);
            return $links;
        } );
        add_action( 'admin_menu', function () {
            add_menu_page( 
                'AI Rights Protection by SimpleFeed', 
                'AI Rights Protection', 
                'manage_options', 
                'simplefeed-bots-protection-menu', 
                function () {
                    include_once self::$_pluginDIR . '/includes/views/settings.php';
                }, 
                plugins_url('wp-simplefeed-bots-protection/includes/img/logo.png'),  null );
        });
    }

    public static function syncAsync($sitemap) {
        (new SFAIRP_SimpleFeedBotsProtectionAdmin())->updateSitemap(wp_json_encode($sitemap));
    }

}