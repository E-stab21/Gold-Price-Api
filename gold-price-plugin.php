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
define( 'MY_GOLD_PRICE_CRON_HOOK', 'my_hourly_sync' );

// Register
register_activation_hook( __FILE__, 'hourly_sync' );
function hourly_sync() {
    if ( ! wp_next_scheduled( MY_GOLD_PRICE_CRON_HOOK ) ) {
        // Schedule the event to run hourly, starting now
        // time() gets the current Unix timestamp
        // 'hourly' is a built-in WordPress cron interval
        wp_schedule_event( time(), 'hourly', MY_GOLD_PRICE_CRON_HOOK );
    }
}

// Hook the function to be executed when the cron event fires
add_action( MY_GOLD_PRICE_CRON_HOOK, 'update_prices' );

// This function will be called when your plugin is deactivated
register_deactivation_hook( __FILE__, 'clear_scheduled_updates' );
function clear_scheduled_updates() {
    // Clear the scheduled event
    wp_clear_scheduled_hook( MY_GOLD_PRICE_CRON_HOOK );
}

