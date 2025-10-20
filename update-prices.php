<?php
function update_prices() {
    require_once('fetch-data.php');
    $updated_count = 0;

    // Ensure WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        error_log("WooCommerce is not active");
        return '';
    }

    $metal_prices = get_prices_metal_api();
    if (!$metal_prices) {
        error_log("No metal api");
        return '';
    }

    $gold_price = get_gold_price();
    $silver_price = get_silver_price();
    $platinum_price = $metal_prices[2];
    $palladium_price = $metal_prices[3];

    $products = wc_get_products(array('limit' => -1));

    foreach ($products as $product) {
        $product_id = $product->get_id();
        $weight = $product->get_meta( '_product_weight_ounces' );
        $markup = $product->get_meta( '_markup' );
        $percent_markup = $product->get_meta( '_percent_markup' );
        $type = $product->get_meta( '_metal_type', true);

        if ($weight) {
            switch ( $type ) {
                case 'Gold':
                    error_log("Gold price is $gold_price");
                    $price = (float)$gold_price * $weight * (1 + $percent_markup) + $markup;
                    break;
                case 'Silver':
                    $price = (float)$silver_price * $weight * (1 + $percent_markup)  + $markup;
                    break;
                case 'Platinum':
                    $price = (float)$platinum_price * $weight * (1 + $percent_markup)  + $markup;
                    break;
                case 'Palladium':
                    $price = (float)$palladium_price * $weight * (1 + $percent_markup)  + $markup;
                    break;
                case '': // N/A or not specified
                    // Handle as appropriate, maybe skip calculation or default to 24K
                    error_log( 'MY_GOLD_PRICE_PLUGIN: Product #{$product_id} has no metal selected. skipping pricing.' );
                    continue 2;
                    default: continue 2;
            }
        } else {
            $price = '';
        }

        if ( (float) $product->get_regular_price() !== $price ) {
            $product->set_regular_price( $price );
            $product->set_price( $price ); // Set active price to regular price

            // If you had a sale price calculation:
            // $product->set_sale_price( $new_sale_price );

            $product->save(); // Save all changes to the product object
            $updated_count++;
            error_log( "MY_GOLD_PRICE_PLUGIN: Updated product #{$product_id} to price: {$price}" );
        }
    }
    error_log( "MY_GOLD_PRICE_PLUGIN: Updated #{$updated_count} products");
}

