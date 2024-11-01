<?php
class ValidateCF7 {
    function __construct() {
        add_action( 'admin_init', array( $this, 'check_cf7_is_active_status' ) );

        if ( ! self::get_plugin_status() ) {
            return;
        }
    }

    // The primary sanity check, automatically disable the plugin on activation if it doesn't
    // meet minimum requirements.
    static function activation_check() {
        if ( ! self::get_plugin_status() ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die( __( 'This Plugin required Contact Form 7 Plugin to be installed and activated.', 'validate-cf7' ) );
        }
    }

    function check_cf7_is_active_status() {
        if ( ! self::get_plugin_status() ) {
            if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
                deactivate_plugins( plugin_basename( __FILE__ ) );
                add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        }
    }

    function disabled_notice() {
       echo '<strong>' . esc_html__( 'This Plugin required Contact Form 7 Plugin to be installed and activated.', 'validate-cf7' ) . '</strong>';
    }

    static function get_plugin_status() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( !is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
             return false;
         }
        return true;
    }
}