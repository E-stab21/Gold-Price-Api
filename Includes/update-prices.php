<?php
function update_prices() {
    $updated_count = 0;

    // Ensure WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    $gold_price = get_gold_price();
    if ( ! $gold_price ) {
        return;
    }

    $silver_price = get_silver_price();
    if ( ! $silver_price ) {
        return;
    }

    $platinum_price = get_platinum_price();
    if ( ! $platinum_price ) {
        return;
    }

    $products = wc_get_products(array('limit' => -1));

    foreach ($products as $product) {
        $product_id = $product->get_id();
        $weight = $product->get_meta_data( '_product_weight_ounces' );
        $markup = $product->get_meta_data( '_markup' );
        $type = $product->get_meta_data( '_metal_type' );

        switch ( $type ) {
            case 'Gold':
                $price = (float)$gold_price * $weight + $markup;
                break;
            case 'Silver':
                $price = (float)$silver_price * $weight + $markup;
                break;
            case 'Platinum':
                $price = (float)$platinum_price * $weight + $markup;
                break;
            case '': // N/A or not specified
                // Handle as appropriate, maybe skip calculation or default to 24K
                error_log( 'MY_GOLD_PRICE_PLUGIN: Product #{$product_id} has no metal selected. Defaulting to 0.' );
                $price = 0;
                break;
                default:
                    $price = 0;
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

