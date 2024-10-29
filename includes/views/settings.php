<?php 
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly 
    }
    require_once dirname(__FILE__).'/../classes/SFAIRP_SimpleFeedBotsProtectionSettings.php';
?>

<h2>
    <div style="font-family: Inter;font-size: 30px;display: flex; align-items: flex-end;">
        <div>
            <img src="<?php echo esc_url(plugin_dir_url( __FILE__ )) . '../img/SFLogo.png'; ?>">
        </div>
        <div style="display: flex;align-items: flex-end;flex-direction: column;justify-content: center;">
            <div style="">AI Rights Protection</div>
            <div style="font-size: 13px;margin-top: 3px;">by <a href="https://www.simplefeed.com/solutions/ai-protection/" target="_blank">SimpleFeed</a></s>
        </div>        
    </div>
    
</h2>


<span>
  <form id="simplefeed_bots_protection_settings_form" method='post' action='options.php' style="height:100%">
    <?php settings_fields( SFAIRP_SimpleFeedBotsProtectionSettings::$GROUP ); ?>

    <div style="display: inline-flex;">
        <!-- Enable bot protection -->
        <div style="width: 23rem; height: auto; padding: 0.7rem; border-radius: 0.5rem; background: white; margin: 0rem 0.4rem 0rem 0rem;">
            <div style="font-family: Inter; font-size: 0.9rem; font-weight: 600;">Bot Protection</div>
            <div style="font-family: Inter;line-height: 1.3rem;width: 80%;padding-top: 0.3rem;">Block AI bots from scraping your content and customize bot access settings.</div>
            <div style="padding-top: 0.3rem;">
                <label>                
                    <input type="hidden" name="simplefeed_bots_protection_settings_enabled" value="no"></input>
                    <input type="checkbox" name="simplefeed_bots_protection_settings_enabled" value="yes" 
                        <?php if (esc_attr( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ENABLED) ) == 'yes') { echo "checked"; } ?>
                    ></input>
                    <span class="simplefeed_bots_protection_settings_enabled_slider"></span>
                    <b><?php if (esc_attr( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ENABLED) ) == 'yes') { echo "On"; } else {echo "Off"; } ?></b>
                </label>
            </div>
        </div>
        <!-- Enable SimpleFeed Backup-->
        <!-- <div class="<?php if (SFAIRP_SimpleFeedBotsProtectionSettings::getP(false)!=true) { echo 'need_premium'; };?>" style="width: 23rem; height: auto; padding: 0.7rem; border-radius: 0.5rem; background: white; margin: 0rem 0.4rem 0rem 0rem;">
            <div style="font-family: Inter; font-size: 0.9rem; font-weight: 600;">SimpleFeed Backup</div>
            <div style="font-family: Inter;line-height: 1.3rem;width: 80%;padding-top: 0.3rem;">Synchronize your blocked bot list with SimpleFeed to get protection the latest AI bots.</div>
            <div style="padding-top: 0.3rem;">
                <label>                
                    <input type="hidden" name="simplefeed_bots_protection_settings_enabled_backup" value="no"></input>
                    <input type="checkbox" name="simplefeed_bots_protection_settings_enabled_backup" value="yes" 
                        <?php if (esc_attr( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ENABLED_BACKUP) ) == 'yes') { echo "checked"; } ?>
                    ></input>
                    <span class="simplefeed_bots_protection_settings_enabled_backup_slider"></span>
                    <b><?php if (esc_attr( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ENABLED_BACKUP) ) == 'yes') { echo "On"; } else {echo "Off"; } ?></b>
                </label>
            </div>
        </div>-->
        <!--BOTS settings -->
        <!-- <div style="width: 23rem; height: auto; padding: 0.7rem; border-radius: 0.5rem; background: white; margin: 0rem 0.4rem 0rem 0rem;">
            <div style="font-family: Inter; font-size: 0.9rem; font-weight: 600;">Bots Detection Rules (as Json)</div>
            <div style="font-family: Inter;line-height: 1.3rem;width: 80%;padding-top: 0.3rem;">Set and manage rules to detect and control AI bot access.</div>
            <div style="padding-top: 0.3rem;">
                <label class="<?php if (SFAIRP_SimpleFeedBotsProtectionSettings::getP(false)!=true) { echo 'need_premium'; };?>">      
                    <input type="checkbox" id="asJson" ></input>
                    <span class="asJson_slider"></span>
                    <b id="asJsonLabel">Enable</b>
                </label>
            </div>
        </div>-->
        <!-- robots.txt -->
        <div class="additional <?php if (SFAIRP_SimpleFeedBotsProtectionSettings::getP(false)!=true) { echo 'need_premium'; };?>" style="width: 23rem; height: auto; padding: 0.7rem; border-radius: 0.5rem; background: white; margin: 0rem 0.4rem 0rem 0rem;">
            <div style="font-family: Inter; font-size: 0.9rem; font-weight: 600;">Additional robots.txt rows</div>
            <div style="font-family: Inter;line-height: 1.3rem;width: 80%;padding-top: 0.3rem;">
                Add additional rows to your robots.txt file with this feature. 
                Better to have here the <i>Disallow: ...</i></div>
            <div style="padding-top: 0.3rem;">
                <label>
                    <div id="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$ADDITIONAL_ROBOTSTXT_ROWS);?>_editor"
                        style="height: 200px; width: 100%;padding-left: 1rem;"><?php 
                            echo esc_textarea( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ADDITIONAL_ROBOTSTXT_ROWS) ); 
                        ?>
                    </div>
                    <textarea 
                        style="display: none;"
                        id="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$ADDITIONAL_ROBOTSTXT_ROWS);?>" 
                        name="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$ADDITIONAL_ROBOTSTXT_ROWS);?>" 
                        rows="4"><?php 
                            echo esc_textarea( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ADDITIONAL_ROBOTSTXT_ROWS) );
                        ?></textarea>
                </label>                
            </div>
            <div style="padding-top: 0.4rem; text-align: right;">
                <input type="button" class="button button-primary" 
                    style="background-color: transparent !important;color: #1483d5 !important;" value="Save changes"/>
            </div>
        </div>
    </div>

    <table style='width: 98%; padding-top: 0.8rem;'>        
        <tr valign='top'>            
            <td width="100%">
                <h3 id="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS);?>_header" style="padding-left: 0.3rem;">
                    <?php if (esc_attr( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ENABLED) ) == 'yes') { 
                        echo 'Bot Access Control List';
                    } else {
                        echo 'Please turn on Bot Protection to view bot crawl request log and control access.';
                    };?>                    
                    <button action='add' style='float: right; margin-bottom: 1rem;background: transparent;color: #007bff;<?php if (esc_attr( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ENABLED) ) == 'no' || SFAIRP_SimpleFeedBotsProtectionSettings::getP(false)!=true) { echo 'display:none'; };?>' >
                        Add new restriction
                    </button>
                </h3>
                <!--BOTS settings -->
                <div style="<?php if (esc_attr( get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$ENABLED) ) == 'no') { echo 'display:none !important';};?>">
                    <div id="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS);?>_json">
                        <label>
                            <div id="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS);?>_editor" 
                                style="height: 200px; width: 100%; display:none;"><?php echo esc_html(get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS)); ?>
                            </div>
                            <textarea 
                                style="display: none;"
                                id="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS);?>" 
                                name="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS);?>" 
                                rows="4"><?php echo esc_html(get_option(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS)); ?></textarea>
                        </label>
                    </div>
                    <table id="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS);?>_table" width="100%"></table>
                    <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center;">                        
                        <div><input type="button" id="resetJson" value="Roll back to default rules"></input></div>
                        <div id="<?php echo esc_attr(SFAIRP_SimpleFeedBotsProtectionSettings::$BOTS);?>_pagination" current="1"></div>
                    </div>
                </div>
            </td>            
        </tr>
        <tr>
            <td>
                <div id="simplefeed_bots_protection_history_frame">
                    <div id="simplefeed_bots_protection_history_frame-content">
                        <span id="simplefeed_bots_protection_history_frame-close">&times;</span>
                        <!-- HISTORY -->
                        <h2>"AI Rights Protection by SimpleFeed" Dashboard for <b id="simplefeed_bots_protection_history_for"></b></h2>
                        <p style="padding-left: 0.2rem;"><b>Bots detected</b></p>
                        <table id="simplefeed_bots_protection_history" width="100%" style="border-collapse: collapse;"></table>
                    </div>
                </div>
            </td>
        </tr>
    </table>       
    <div style='display:none'>
        <?php  submit_button(); ?>
    </div>
  </form>
</div>
