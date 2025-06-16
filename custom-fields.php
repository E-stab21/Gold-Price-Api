<?php
// Displays the text inputs
add_action( 'woocommerce_product_options_general_product_data', 'my_custom_product_fields_display' );

// Hook to save custom fields when product is saved/updated
add_action( 'woocommerce_process_product_meta', 'my_custom_product_fields_save' );

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
            'value'       => get_post_meta( $post->ID, '_metal_type', true ), // Current saved value
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
    $markup = isset( $_POST['_markup']) ? sanitize_text_field( $_POST['_markup'] ) : '';
    $product->update_meta_data( '_markup', $markup );

    // Save the weight
    $type = isset( $_POST['_metal_type']) ? sanitize_text_field( $_POST['_metal_type'] ) : '';
    $product->update_meta_data( '_metal_type', $type );

    // Save all changes made to metadata
    $product->save_meta_data(); // This is for metadata specifically
}

