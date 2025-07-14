<?php
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

$inline_script = "
        jQuery(document).ready(function($) {
            $('#weight, #purity').on('input', function() {
                var gold_price = " . ( $gold_price / 31.1035 ) . ";
                var silver_price = " . ( $silver_price / 31.1035 ) . ";
                var weight = parseFloat($('#weight').val()) || 0;
                var purity = $('#purity').val();
        
                let value;
                switch ( purity ) {
                    case '10k':
                        value = gold_price *  weight * 0.417;
                        break;
                    case '14k':
                        value = gold_price *  weight * 0.583;
                        break;
                    case '18k':
                        value = gold_price *  weight * 0.74;
                        break;
                    case '22k':
                        value = gold_price *  weight * 0.916;
                        break;
                    case '24k':
                        value = gold_price *  weight;
                        break;
                    case 'silver':
                        value = silver_price *  weight * 0.925;
                        break;
                    default:
                        value = 0;
                }
        
                $('#final-value').text('Estimated Value: $' + value);
            });
        });
    ";

print($inline_script);
function test( $atts ) {
    // Parse attributes if you want to allow customization, e.g., [value_calculator unit="oz"]
    $atts = shortcode_atts( array(
        'unit' => 'ounce', // Default unit
    ), $atts, 'value_calculator' );

    //checks
    error_log("value calculator is running");

    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'fetch-data' ) ) {
        error_log("woocommerce is not activated");
        return ''; // Return empty string if dependencies not met
    }

    $output = '<form action="' . esc_url( admin_url('admin-post.php') ) . '" method="post">';

    // Nonce for security
    $output .= wp_nonce_field( 'my_form_submission_nonce_action', 'my_form_nonce_field', true, false );

    $output = '<div class="value-calculator-container">';
    $output .= '<div class="title-container">';
    $output .= '<h4 class="title">Value Calculator</h4>';
    $output .= '</div>';
    $output .= '<div class="form-container">';
    $output .= '<form id="value-calculator-form">';
    $output .= '<label for="weight">Weight (grams)</label>';
    $output .= '<input type="number" id="weight" name="weight" value="0.0">';
    $output .= '<label for="purity">Purity</label>';
    $output .= '<select id="purity" name="purity">';
    $output .= '<option value="10k">10k</option>';
    $output .= '<option value="14k">14k</option>';
    $output .= '<option value="18k">18k</option>';
    $output .= '<option value="22k">22k</option>';
    $output .= '<option value="24k">24k</option>';
    $output .= '<option value="silver">Sterling Silver</option>';
    $output .= '</select>';
    $output .= '<input type="submit" value="display-price">';
    $output .= '<h4 id="final-value" class="value">Estimated Value:</h4>';

    return $output;
}

function handle_value_calculator_submission() {
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

    // 1. Verify Nonce for security
    if ( ! isset( $_POST['my_form_nonce_field'] ) || ! wp_verify_nonce( $_POST['my_form_nonce_field'], 'my_form_submission_nonce_action' ) ) {
        error_log("security nonce mismatch");
        wp_die( 'Security check failed!' ); // Or redirect with an error message
    }

    // 2. Sanitize and Validate Input
    $weight = !empty( (int) $_POST['weight'] ) ? (int) $_POST['weight'] : 0;
    $purity = sanitize_text_field( $_POST['user_email'] );

    switch ( $purity ) {
        case '10k':
            $value = $gold_price *  $weight * 0.417;
            break;
        case '14k':
            $value = $gold_price *  $weight * 0.583;
            break;
        case '18k':
            $value = $gold_price *  $weight * 0.74;
            break;
        case '22k':
            $value = $gold_price *  $weight * 0.916;
            break;
        case '24k':
            $value = $gold_price *  $weight;
            break;
        case 'silver':
            $value = $silver_price *  $weight * 0.925;
            break;
        default:
            $value = 0;
    }

    return $value;
}