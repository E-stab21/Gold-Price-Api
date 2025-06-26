<?php
// Displays the text inputs
add_action( 'woocommerce_product_options_general_product_data', 'my_custom_product_fields_display' );

// Hook to save custom fields when product is saved/updated
add_action( 'woocommerce_process_product_meta', 'my_custom_product_fields_save' );

//creates the shortcode
add_shortcode( 'live_metal_price', 'my_metal_price_shortcode_display' );

function my_custom_product_fields_display( $product ) {
    global $woocommerce, $post;

    echo '<div class="options_group">'; // Wraps fields in a group for styling

    //Text Input for Product Weight
    woocommerce_wp_text_input(
        array(
            'id'          => '_product_weight_ounces', // This will be your meta key
            'label'       => __( 'Product Weight (oz)', 'my-text-domain' ), // Label visible in admin
            'placeholder' => 'e.g., 5.0', // Placeholder text
            'description' => __( 'Enter the weight of metal in ounces for this product.', 'my-text-domain' ), // Description
            'desc_tip'    => 'true', // Shows description as a tooltip
            'data_type'   => 'price', // Tells WooCommerce to treat this as a number for validation
        )
    );

    //Text Input for markup
    woocommerce_wp_text_input(
        array(
            'id'          => '_markup', // This will be your meta key
            'label'       => __( 'Markup', 'my-text-domain' ), // Label visible in admin
            'placeholder' => 'e.g., 5.0', // Placeholder text
            'description' => __( 'Enter the markup for this product.', 'my-text-domain' ), // Description
            'desc_tip'    => 'true', // Shows description as a tooltip
            'data_type'   => 'price', // Tells WooCommerce to treat this as a number for validation
        )
    );

    //Text Input for percentage markup
    woocommerce_wp_text_input(
        array(
            'id'          => '_percent_markup', // This will be your meta key
            'label'       => __( 'Percent Markup', 'my-text-domain' ), // Label visible in admin
            'placeholder' => 'e.g., 0.2', // Placeholder text
            'description' => __( 'Enter the percentage markup for this product.', 'my-text-domain' ), // Description
            'desc_tip'    => 'true', // Shows description as a tooltip
            'data_type'   => 'price', // Tells WooCommerce to treat this as a number for validation
        )
    );

    // Example 4: A dropdown select field
    woocommerce_wp_select(
        array(
            'id'          => '_metal_type',
            'label'       => __( 'Metal type', 'my-text-domain' ),
            'placeholder' => __( 'Select Metal', 'my-text-domain' ),
            'description' => __( 'Select the metal for this product.', 'my-text-domain' ),
            'options'     => array( // Options for the dropdown
                'Gold' => __( 'Gold', 'my-text-domain' ),
                'Silver' => __( 'Silver', 'my-text-domain' ),
                'Platinum' => __( 'Platinum', 'my-text-domain' ),
                // Add more as needed
            ),
            'value'       => get_meta( $product->ID, '_metal_type', true ), // Current saved value
        )
    );

    echo '</div>'; // Close the options_group div
}

function my_custom_product_fields_save( $post_id ) {
    $product = wc_get_product( $post_id );

    // Save the weight
    $weight = isset( $_POST['_product_weight_ounces']) ? sanitize_text_field( $_POST['_product_weight_ounces'] ) : '';
    $product->update_meta_data( '_product_weight_ounces', $weight );
    error_log( print_r( $weight, true ) );

    // Save the markup
    $markup = isset( $_POST['_markup']) ? sanitize_text_field( $_POST['_markup'] ) : 0;
    $product->update_meta_data( '_markup', $markup );

    // Save the percent markup
    $percent_markup = isset( $_POST['_percent_markup']) ? sanitize_text_field( $_POST['_percent_markup'] ) : 0;
    $product->update_meta_data( '_percent_markup', $percent_markup );

    // Save the weight
    $type = isset( $_POST['_metal_type']) ? sanitize_text_field( $_POST['_metal_type'] ) : '';
    $product->update_meta_data( '_metal_type', $type );

    // Save all changes made to metadata
    $product->save_meta_data(); // This is for metadata specifically
}

function my_metal_price_shortcode_display( $atts ) {
    require_once('fetch-data.php');

    // Parse attributes if you want to allow customization, e.g., [live_metal_price unit="gram"]
    $atts = shortcode_atts( array(
        'unit' => 'ounce', // Default unit
    ), $atts, 'live_metal_price' );

    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'fetch-data' ) ) {
        return ''; // Return empty string if dependencies not met
    }

    $gold_price = get_gold_price();
    if ( ! $gold_price ) {
        error_log("Gold price is empty");
        return '';
    }

    $silver_price = get_silver_price();
    if ( ! $silver_price ) {
        error_log("Silver Price is empty");
        return '';
    }

    $formatted_price = '';
    if ( $atts['unit'] === 'gram' ) {
        $formatted_price = "Live Spot Prices: Gold " . strip_tags( wp_price( $gold_price / 31.1035 ) ) . '/g Silver ' . strip_tags( wc_price( $silver_price / 31.1035 ) ) . '/g';
    } else { // Default to ounce
        $formatted_price = "Live Spot Prices: Gold " . strip_tags( wp_price( $gold_price) ) . '/oz Silver ' . strip_tags( wc_price( $silver_price ) ) . '/oz';
    }

    return '<span class="live-metal-price-shortcode">' . esc_html( $formatted_price ) . '</span>';
}

