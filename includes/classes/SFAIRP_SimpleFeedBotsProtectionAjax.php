<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly 
}
require_once 'SFAIRP_SimpleFeedBotsProtectionDatabase.php';
require_once 'SFAIRP_SimpleFeedBotsProtectionSettings.php';
/**
 * Class SFAIRP_SimpleFeedBotsProtectionAjax
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
class SFAIRP_SimpleFeedBotsProtectionAjax
{
    public function __construct() {}

    public static function register($pluginFILE) {
        add_action('wp_ajax_wp_simplefeed_bots_protection_history', array(__CLASS__, 'handleHistory'));
        add_action('wp_ajax_wp_simplefeed_bots_protection_history_action_enable', array(__CLASS__, 'handleHistoryActionEnable'));
        add_action('wp_ajax_wp_simplefeed_bots_protection_history_action_disable', array(__CLASS__, 'handleHistoryActionDisable'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueueAdminScripts'),10,1);
        add_action('wp_ajax_wp_simplefeed_bots_protection_log', array(__CLASS__, 'handleLog'));
    }

    public static function enqueueAdminStyles($hook) {        
    }

    public static function enqueueAdminScripts($hook) {        
        if (strpos($hook,'simplefeed-bots-protection-menu')!== false) {
            wp_enqueue_style('wp_simplefeed_bots_protection_style', plugins_url('../css/style.css', __FILE__), array(), SIMPLEFEED_BOTS_PROTECTION_VERSION, 'all' );
            wp_enqueue_script('wp_simplefeed_bots_protection_script', plugins_url('../js/script.js', __FILE__), array('jquery'), SIMPLEFEED_BOTS_PROTECTION_VERSION, true);
            wp_localize_script('wp_simplefeed_bots_protection_script', 'MyAjax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('my_ajax_nonce'),
                'bots' => SFAIRP_SimpleFeedBotsProtectionSettings::getBots(),
                'default' => SFAIRP_SimpleFeedBotsProtection::$BOTS_DEFAULT,
                'home_url' => home_url(''),
                'types' => get_post_types( array('public' => true) ),
                'categories' => get_categories(),
                'tags' => get_tags(),
                'P' => SFAIRP_SimpleFeedBotsProtectionSettings::getP(true)
            ));
            wp_enqueue_script('wp_simplefeed_bots_protection_acejs', plugins_url('../js/ace@1.4.12.js', __FILE__), array(), SIMPLEFEED_BOTS_PROTECTION_VERSION, true);
        }
    }

    public static function handleHistory() {
        $groupByBot = isset($_POST['group_by_bot']) ? sanitize_text_field(wp_unslash($_POST['group_by_bot'])) : null; 
        if ($groupByBot==true) {
            $list = SFAIRP_SimpleFeedBotsProtectionDatabase::selectBotMetrics(); 
        } else {
            $limit = isset($_POST['limit']) ? sanitize_text_field(wp_unslash($_POST['limit'])) : null; 
            $list = isset($_POST['bot']) ? SFAIRP_SimpleFeedBotsProtectionDatabase::selectSitemaps(sanitize_text_field(wp_unslash($_POST['bot'])),$limit) : SFAIRP_SimpleFeedBotsProtectionDatabase::selectSitemaps(null,$limit);
        }        
        $resp = array(
            'list' => $list
        );
        wp_send_json_success($resp);
    }

    public static function handleLog() {
        $resp = array(
            'list' => SFAIRP_SimpleFeedBotsProtectionDatabase::selectLog()
        );
        wp_send_json_success($resp);
    }


    public static function handleHistoryActionEnable() {
        $resp = array(
            'success' => false
        );
        if (isset($_POST['token'])) {
            SFAIRP_SimpleFeedBotsProtectionDatabase::updateSitemapEnable(sanitize_text_field(wp_unslash($_POST['token'])));
            $resp['success'] = true;
        }
        if (isset($_POST['bot'])) {
            SFAIRP_SimpleFeedBotsProtectionDatabase::updateSitemapEnableForBot(sanitize_text_field(wp_unslash($_POST['bot'])));
            $resp['success'] = true;
        }
        wp_send_json_success($resp);
    }

    public static function handleHistoryActionDisable() {
        $resp = array(
            'success' => false
        );
        if (isset($_POST['token'])) {
            SFAIRP_SimpleFeedBotsProtectionDatabase::updateSitemapDisable(sanitize_text_field(wp_unslash($_POST['token'])));
            $resp['success'] = true;
        }
        if (isset($_POST['bot'])) {
            SFAIRP_SimpleFeedBotsProtectionDatabase::updateSitemapDisableForBot(sanitize_text_field(wp_unslash($_POST['bot'])));
            $resp['success'] = true;
        }
        wp_send_json_success($resp);
    }

}