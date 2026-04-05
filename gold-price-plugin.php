<?php
/**
 * Plugin Name: Gold Price Plugin
 * Version: 1.0.0
 * Author: Ethan Stabenow
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include_once( 'custom-fields.php' );
require_once( 'update-prices.php' );

// Scheduling --------------------------------------------------------------------
define( 'MY_GOLD_PRICE_CRON_HOOK', 'my_price_update_event' );

// Custom interval
add_filter( 'cron_schedules', 'my_custom_interval' );
function my_custom_interval( $schedules ) {
    $schedules['every_minute'] = array(
        'interval' => 120,
        'display' => esc_html( 'Every Minute' )
    );
    return $schedules;
}

// Register
register_activation_hook( __FILE__, 'my_plugin_activation' );
function my_plugin_activation() {
    if ( ! wp_next_scheduled( MY_GOLD_PRICE_CRON_HOOK ) ) {
        // Schedule the event to run hourly, starting now
        // time() gets the current Unix timestamp
        // 'hourly' is a built-in WordPress cron interval
        wp_schedule_event( time(), 'every_minute', MY_GOLD_PRICE_CRON_HOOK );
    }
}

// Deactivation
register_deactivation_hook( __FILE__, 'my_plugin_deactivation' );
function my_plugin_deactivation() {
    // Clear the scheduled event
    wp_clear_scheduled_hook( MY_GOLD_PRICE_CRON_HOOK );
}

// Hook the function to be executed when the cron event fires
add_action( MY_GOLD_PRICE_CRON_HOOK, 'update_prices' );
