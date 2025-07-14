<?php
// Displays the product inputs
add_action( 'woocommerce_product_options_general_product_data', 'my_custom_product_fields_display' );

// Hook to save custom fields when product is saved/updated
add_action( 'woocommerce_process_product_meta', 'my_custom_product_fields_save' );

//creates the live spot price shortcode
add_shortcode( 'live_metal_price', 'my_metal_price_shortcode_display' );

//creates the value calculator shortcode
add_shortcode( 'value_calculator', 'my_value_calculator_shortcode_display' );

function my_custom_product_fields_display() {
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
            'data_type'   => 'decimal', // Tells WooCommerce to treat this as a number for validation
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
            'data_type'   => 'decimal', // Tells WooCommerce to treat this as a number for validation
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
            'data_type'   => 'decimal', // Tells WooCommerce to treat this as a number for validation
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
                'Dental_Alloy' => __( 'Dental Alloy', 'my-text-domain' ),
                // Add more as needed
            ),
            'value' => get_post_meta( $post->ID, '_metal_type', true ), // Current saved value
        )
    );

    echo '</div>'; // Close the options_group div
}

function my_custom_product_fields_save( $post_id ) {
    $product = wc_get_product( $post_id );

    // Save the weight
    $weight = is_numeric( $_POST['_product_weight_ounces'] ) && $_POST['_product_weight_ounces'] > 0  ? sanitize_text_field( $_POST['_product_weight_ounces'] ) : 0;
    $product->update_meta_data( '_product_weight_ounces', $weight );

    // Save the markup
    $markup = is_numeric( $_POST['_markup'] ) && $_POST['_markup'] > 0 ? sanitize_text_field( $_POST['_markup'] ) : 0;
    $product->update_meta_data( '_markup', $markup );

    // Save the percent markup
    $percent_markup =  is_numeric( $_POST['_percent_markup'] ) && $_POST['_percent_markup'] < 1 && $_POST['_percent_markup'] > 0 ? sanitize_text_field( $_POST['_percent_markup'] ) : 0;
    $product->update_meta_data( '_percent_markup', $percent_markup );

    // Save the metal type
    $type = !empty( $_POST['_metal_type'] ) ? sanitize_text_field( $_POST['_metal_type'] ) : '';
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

    if ( ! class_exists( 'WooCommerce' )) {
        error_log("Live price shortcode woocommerce is not active");
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
        $formatted_price = "Live Spot Prices: Gold $" . number_format( $gold_price / 31.1035, 2 ) . '/g Silver $' . strip_tags( wc_price( $silver_price / 31.1035 ) ) . '/g';
    } else { // Default to ounce
        $formatted_price = 'Live Spot Prices: Gold $' . number_format( $gold_price, 2 ) . ' Silver $' . number_format( $silver_price, 2 );
    }

    return '<span class="live-metal-price-shortcode">' . esc_html( $formatted_price ) . '</span>';
}

function my_value_calculator_shortcode_display() {
    require_once('fetch-data.php');

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

    // 1. Enqueue a script to attach our inline script to.
    // or register a 'dummy' handle if our inline script has no external dependencies.
    wp_enqueue_script( 'value-calculator-script', plugins_url( 'empty.js', __FILE__ ), array('jquery'), null, true );
    // The second parameter is empty because we don't have an external file.
    // array('jquery') makes sure jQuery is loaded before our inline script.
    // null for version, true for loading in footer.

    // 2. Define the inline JavaScript.
    // It's good practice to wrap your JS in a self-executing anonymous function
    // to avoid global variable conflicts.
    $inline_script = "
        jQuery(document).ready(function($) {
            $('#weight, #purity').on('input', function() {
                var gold_price = " . number_format( $gold_price / 31.1, 2 ) . ";
                var silver_price = " . number_format( $silver_price / 31.1, 2 ) . ";
                var weight = parseFloat($('#weight').val()) || 0;
                var purity = $('#purity').val();
        
                let value;
                switch ( purity ) {
                    case '10k':
                        value = gold_price *  weight * 0.417 * 0.75;
                        break;
                    case '14k':
                        value = gold_price *  weight * 0.583 * 0.75;
                        break;
                    case '18k':
                        value = gold_price *  weight * 0.74 * 0.75;
                        break;
                    case '22k':
                        value = gold_price *  weight * 0.916 * 0.75;
                        break;
                    case '24k':
                        value = gold_price *  weight * 0.75;
                        break;
                    case 'silver':
                        value = silver_price *  weight * 0.925 * 0.75;
                        break;
                    default:
                        value = 0;
                }
				
				value = parseFloat(value.toFixed(2));
        
                $('#final-value').text('Estimated Value: $' + value);
            });
        });
    ";

    if ( ! wp_add_inline_script( 'value-calculator-script', $inline_script, 'after' ) ) {
        error_log("inline add broke");
        return '';
    }

    ob_start(); // Start output buffering
    ?>
    <div class="value-calculator-container">
        <div class="title-container">
            <h4 class="title">Value Calculator</h4>
        </div>
        <div class="form-container">
            <form id="value-calculator-form">
                <label for="weight">Weight (grams)</label>
                <input type="number" id="weight" name="weight" value="">
                <label for="purity">Purity</label>
                <select id="purity" name="purity">
                    <option value="10k">10k</option>
                    <option value="14k">14k</option>
                    <option value="18k">18k</option>
                    <option value="22k">22k</option>
                    <option value="24k">24k</option>
                    <option value="silver">Sterling Silver</option>
                </select>
            </form>
            <h4 id="final-value" class="value">Estimated Value:</h4>
        </div>
    </div>
    <?php
    error_log("buffered");
    return ob_get_clean(); // Return the buffered HTML
}