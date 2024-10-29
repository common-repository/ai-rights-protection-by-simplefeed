<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly 
}
require_once ABSPATH . 'wp-admin/includes/upgrade.php' ;
/**
 * Class SFAIRP_SimpleFeedBotsProtectionDatabase
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
class SFAIRP_SimpleFeedBotsProtectionDatabase
{

    public function __construct() {}

    public static function createTableSitemaps() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$wpdb->prefix}simplefeed_bots_protection_sitemaps (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            ip tinytext NOT NULL,
            bot tinytext NOT NULL,
            token text NOT NULL,
            robots_txt_requests int(11) NOT NULL DEFAULT 1,
            robots_txt_requests_200 int(11) NOT NULL DEFAULT 0,
            robots_txt_requests_403 int(11) NOT NULL DEFAULT 0,
            enable Boolean DEFAULT false,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        dbDelta($sql);

    }

    public static function createTableSitemapsRequests() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$wpdb->prefix}simplefeed_bots_protection_sitemaps_requests (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            token text NOT NULL,
            ip tinytext NOT NULL,
            user_agent text NOT NULL,
            enable Boolean DEFAULT false,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        dbDelta($sql);

    }

    public static function deleteTableSitemaps(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}simplefeed_bots_protection_sitemaps");
    }

    public static function deleteTableSitemapsRequests(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}simplefeed_bots_protection_sitemaps_requests");
    }

    public static function createTableLogs(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$wpdb->prefix}simplefeed_bots_protection_logs (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            text text NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        dbDelta($sql);
    }

    public static function deleteTableLogs(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}simplefeed_bots_protection_logs");
    }

    public static function createDatabase() {
        self::createTableSitemaps();
        self::createTableSitemapsRequests();
        self::createTableLogs();
    }

    public static function deleteDatabase() {
        self::deleteTableSitemaps();
        self::deleteTableSitemapsRequests();
        self::deleteTableLogs();
    }

    public static function insertSitemap($ip,$userAgent,$bot,$token,$enable1,$enable2) {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps',
            array(
                'time' => current_time('mysql'),
                'ip' => $ip,
                'bot' => $bot,
                'token' => $token,
                'enable' => $enable1
            )
        );
        $wpdb->insert(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps_requests',
            array(
                'time' => current_time('mysql'),
                'token' => $token,
                'user_agent' => $userAgent,
                'ip' => $ip,
                'enable' => $enable2
            )
        );
    }

    public static function insertLog($text) {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'simplefeed_bots_protection_logs',
            array(
                'time' => current_time('mysql'),
                'text' => $text
            )
        );
    }

    public static function updateSitemap($id,$token,$userAgent,$ip,$robots_txt_requests,$robots_txt_requests_200,$robots_txt_requests_403,$enable) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps',
            array(
                'robots_txt_requests' => $robots_txt_requests,                
                'robots_txt_requests_200' => $robots_txt_requests_200,
                'robots_txt_requests_403' => $robots_txt_requests_403
            ),
            array('id' => $id)
        );
        $wpdb->insert(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps_requests',
            array(
                'time' => current_time('mysql'),
                'token' => $token,
                'user_agent' => $userAgent,
                'ip' => $ip,
                'enable' => $enable
            )
        );
    }
    
    public static function updateSitemapEnable($token) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps',
            array(
                'enable' => true
            ),
            array('token' => $token)
        );
    }

    public static function updateSitemapEnableForBot($bot) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps',
            array(
                'enable' => true
            ),
            array('bot' => $bot)
        );
    }

    public static function updateSitemapDisable($token) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps',
            array(
                'enable' => false
            ),
            array('token' => $token)
        );
    }

    public static function updateSitemapDisableForBot($bot) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps',
            array(
                'enable' => false
            ),
            array('bot' => $bot)
        );
    }

    public static function selectLog() {
        global $wpdb;
        return $wpdb->get_results( 
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}simplefeed_bots_protection_logs ORDER BY id DESC LIMIT %d",1000) 
        );
    }

    
    public static function selectBotMetrics() {
        global $wpdb;
        return $wpdb->get_results("
            SELECT 
                SM.bot,
                POSITION( '1' in GROUP_CONCAT(SM.enable) )>0 AS has_enabled,
                POSITION( '0' in GROUP_CONCAT(SM.enable) )>0 AS has_disabled,
                MAX(R.time) AS last_time,
                COUNT(DISTINCT R.token) AS tokens,
                SUBSTRING_INDEX(GROUP_CONCAT(R.ip ORDER BY R.time DESC), ',', 1) AS last_ip,
                SUBSTRING_INDEX(GROUP_CONCAT(R.enable ORDER BY R.time DESC), ',', 1) AS last_enable,
                count(*) as count
            FROM 
                wp_simplefeed_bots_protection_sitemaps_requests R 
            LEFT JOIN 
                wp_simplefeed_bots_protection_sitemaps SM 
            ON SM.token = R.token
            GROUP BY 
                SM.bot;
        ");
    }
    
    public static function selectSitemaps($bot,$limit) {
        global $wpdb;
        if ($bot!=null) {
            return $wpdb->get_results($wpdb->prepare("
                SELECT 
                    A.*,
                    B.history 
                FROM 
                    {$wpdb->prefix}simplefeed_bots_protection_sitemaps A
                JOIN (
                    SELECT 
                        token,
                        GROUP_CONCAT(CONCAT(time, '=', IP, '=', enable,'=', user_agent) ORDER BY time DESC SEPARATOR '~') AS history
                    FROM (
                        SELECT 
                            token,
                            time,
                            IP,
                            user_agent,
                            enable,
                            ROW_NUMBER() OVER (PARTITION BY token ORDER BY time DESC) AS rn
                        FROM 
                            wp_simplefeed_bots_protection_sitemaps_requests
                    ) AS limited_requests
                    WHERE rn <= %d
                    GROUP BY 
                        token
                ) B ON A.token = B.token
                WHERE A.bot = %s
                ORDER BY 
                    A.time DESC;
            ",$limit!=null ? $limit : 1000, $bot)); 
        } else {
            return $wpdb->get_results($wpdb->prepare("
                SELECT 
                    A.*,
                    B.history 
                FROM 
                    {$wpdb->prefix}simplefeed_bots_protection_sitemaps A
                JOIN (
                    SELECT 
                        token,
                        GROUP_CONCAT(CONCAT(time, '=', IP, '=', enable,'=', user_agent) ORDER BY time DESC SEPARATOR '~') AS history
                    FROM (
                        SELECT 
                            token,
                            time,
                            IP,
                            user_agent,
                            enable,
                            ROW_NUMBER() OVER (PARTITION BY token ORDER BY time DESC) AS rn
                        FROM 
                            wp_simplefeed_bots_protection_sitemaps_requests
                    ) AS limited_requests
                    WHERE rn <= %d
                    GROUP BY 
                        token
                ) B ON A.token = B.token
                ORDER BY 
                    A.time DESC;
            ",$limit!=null? $limit : 1000)); 
        } 
    }

    public static function selectSitemapLastByIpAndBot($ip,$bot) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}simplefeed_bots_protection_sitemaps WHERE ip=%s AND bot=%s ORDER BY time DESC LIMIT 1",$ip,$bot));
    }

    public static function selectSitemapLastByToken($token) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}simplefeed_bots_protection_sitemaps WHERE token=%s ORDER BY time DESC LIMIT 1",$token));
    }

    public static function deleteSitemap($id) {
        global $wpdb;
        $wpdb->delete(
            $wpdb->prefix . 'simplefeed_bots_protection_sitemaps',
            array('id' => $id)
        );
    }

    public static function register($pluginFILE) {
        register_activation_hook($pluginFILE, array(__CLASS__, 'createDatabase'));
        register_deactivation_hook($pluginFILE, array(__CLASS__, 'deleteDatabase'));
    }

}