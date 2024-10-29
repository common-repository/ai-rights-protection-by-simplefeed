<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
/**
 * Plugin Name: AI Rights Protection by SimpleFeed
 * Plugin URI: http://www.simplefeed.com/
 * Description: Plugin for Bots Protection from SimpleFeed Inc.
 * Version: 1.0.21
 * Author: SimpleFeed Inc
 * Author URI: https://simplefeed.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
**/

/* This plugin is licensed software for the use of SimpleFeed customers only. Copyright ©2004 - 2024 SimpleFeed, Inc. All Rights Reserved. Protected by Patents 8065383 and 8661001. */

define('SIMPLEFEED_BOTS_PROTECTION_VERSION', '1.0.21');

require_once plugin_dir_path(__FILE__).'includes/classes/SFAIRP_SimpleFeedBotsProtection.php';
require_once plugin_dir_path(__FILE__).'includes/classes/SFAIRP_SimpleFeedBotsProtectionSettings.php';
require_once plugin_dir_path(__FILE__).'includes/classes/SFAIRP_SimpleFeedBotsProtectionAjax.php';

SFAIRP_SimpleFeedBotsProtection::register(__FILE__,__DIR__);
SFAIRP_SimpleFeedBotsProtectionSettings::register(__FILE__);
SFAIRP_SimpleFeedBotsProtectionAjax::register(__FILE__);
