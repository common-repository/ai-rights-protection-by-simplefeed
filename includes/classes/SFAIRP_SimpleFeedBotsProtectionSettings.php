<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly 
}
require_once 'SFAIRP_SimpleFeedBotsProtectionAdmin.php';
/**
 * Class SFAIRP_SimpleFeedBotsProtectionSettings
 *
 * This class represents the settings for a "AI Rights Protection by SimpleFeed".
 * For settings
 
 * @category Settings
 * @package  Settings
 * @author   alex <a.khomichenko@simplefeed.com>
 * @license  https://simplefeed.com SimpleFeed
 * @link     https://simplefeed.com
 * 
 * This class represents the settings for a "AI Rights Protection by SimpleFeed".
 */

class SFAIRP_SimpleFeedBotsProtectionSettings
{

    public static $GROUP = 'simplefeed_bots_protection_settings';
    public static $ENABLED = 'simplefeed_bots_protection_settings_enabled';
    public static $ENABLED_BACKUP = 'simplefeed_bots_protection_settings_enabled_backup';

    public static $BOTS = 'simplefeed_bots_protection_settings_bots';
    public static $ADDITIONAL_ROBOTSTXT_ROWS = 'simplefeed_bots_protection_settings_additional_robotstxt_rows';

    public static $P = 'simplefeed_bots_protection_settings_p';

    public function __construct() {}

    public static function register($pluginFILE) {
        add_action( 'admin_init', array(__CLASS__, 'registerSettings'));
        add_action( 'admin_menu', array(__CLASS__, 'renderSettings'));
        if (self::isEnabledBackup()) {
            add_action( 'simplefeed_boots_protection_settings_sync', array(__CLASS__, 'syncAsync'), 10, 2);
        }        
    }

    private static function _registerSettingsInner() {
        $settings = array();        

        register_setting( self::$GROUP, self::$ENABLED, 
            array(
                'type' => 'string', 
                'default' => 'yes'
            )
        );
        array_push($settings,self::$ENABLED);

        register_setting( self::$GROUP, self::$ENABLED_BACKUP, 
            array(
                'type' => 'string', 
                'default' => 'yes'
            )
        );
        array_push($settings,self::$ENABLED_BACKUP);

        register_setting( self::$GROUP, self::$BOTS, 
            array(
                'type' => 'string', 
                'default' => ''.wp_json_encode(SFAIRP_SimpleFeedBotsProtection::$BOTS_DEFAULT).'',
                'sanitize_callback' => function ($input) {
                    $decoded = json_decode($input, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return '';
                    }
                    return wp_json_encode($decoded);
                }
            )
        );
        array_push($settings,self::$BOTS);

        register_setting( self::$GROUP, self::$ADDITIONAL_ROBOTSTXT_ROWS, 
            array(
                'type' => 'string'
            )
        );
        array_push($settings,self::$ADDITIONAL_ROBOTSTXT_ROWS);
        return $settings;
    }

    public static function registerSettings() {        
        if ( ( isset($_GET['page']) && $_GET['page']=="simplefeed-bots-protection-menu"  ) || ( isset($_GET['page']) && $_GET['page']=="simplefeed-bots-protection-settings"  ) ) {
            // on show
            if ( isset($_GET['settings-updated'])==false) {
                $settings = self::_registerSettingsInner();
                if (self::isEnabledBackup()) {
                    wp_schedule_single_event(time(),'simplefeed_boots_protection_settings_sync',array('get',null));
                }
            }            
        }
        if (isset($_POST['submit']) && isset($_POST['option_page']) && $_POST['option_page']==self::$GROUP) {
            // on update
            $settings = self::_registerSettingsInner();
            $options = array();
            for ($i = 0; $i < count($settings); $i++) {
                if (isset($_POST[$settings[$i]])) {
                    $s = sanitize_text_field(wp_unslash($_POST[$settings[$i]]));
                    if ($settings[$i]===self::$ADDITIONAL_ROBOTSTXT_ROWS) {
                        $s = sanitize_textarea_field(wp_unslash($_POST[$settings[$i]]));
                    }
                    update_option($settings[$i], $s);
                    if ($settings[$i]===self::$BOTS) {
                        $options[$settings[$i]] = json_decode(stripslashes($s), true);
                    } else {
                        $options[$settings[$i]] = $s;
                    }
                }                
            }
            if (self::isEnabledBackup()) {
                wp_schedule_single_event(time(),'simplefeed_boots_protection_settings_sync',array('set',$options));
            }            
        }
    }

    public static function syncAsync($direction,$options) {
        if ($direction=='get') {
            $settings = (new SFAIRP_SimpleFeedBotsProtectionAdmin())->loadSettings();
            if ($settings!=null) {
                update_option(self::$P, $settings->p);
                update_option(self::$BOTS, wp_json_encode($settings->json->{self::$BOTS}));
                update_option(self::$ENABLED, $settings->json->{self::$ENABLED});
                update_option(self::$ENABLED_BACKUP, $settings->json->{self::$ENABLED_BACKUP});
                update_option(self::$ADDITIONAL_ROBOTSTXT_ROWS, $settings->json->{self::$ADDITIONAL_ROBOTSTXT_ROWS});
            }
        }
        if ($direction==='set') {
            (new SFAIRP_SimpleFeedBotsProtectionAdmin())->updateSettings(wp_json_encode($options));
        }
    }

    public static function isEnabled() {
        return get_option(self::$ENABLED)=='yes';
    }

    public static function isEnabledBackup() {
        $o = get_option(self::$ENABLED_BACKUP); // yes or no
        return $o==false || $o==='yes';
    }

    public static function getAdditionalRobotsTxtRows() {
        return get_option(self::$ADDITIONAL_ROBOTSTXT_ROWS);
    }

    public static function getP($force) {
        if (isset($force) && $force==true) {
            $settings = (new SFAIRP_SimpleFeedBotsProtectionAdmin())->loadSettings();
            if ($settings!=null) {
                $P = $settings->p;
                update_option(self::$P, $P);
            } else {
                $P = get_option(self::$P);
            }
        } else {
            $P = get_option(self::$P);
        }
        return isset($P) && $P==true;
    }

    public static function getBots() {
        if (get_option(self::$BOTS)) {
            return json_decode(get_option(self::$BOTS), true);
        } else {
            return SFAIRP_SimpleFeedBotsProtection::$BOTS_DEFAULT;
        }
    }

    public static function renderSettings() {
        add_options_page(
            '"AI Rights Protection by SimpleFeed" Settings', 
            'AI Rights Protection by SimpleFeed', 
            'manage_options', 
            'simplefeed-bots-protection-settings', 
            function () {
                include_once plugin_dir_path(__FILE__) . '../views/settings.php';
            });
    }

}