<?php
// get gold price
function get_gold_price()
{
    $url = 'https://www.goldapi.io/api/XAU/USD';
    $headers = array(
        'x-access-token: YOUR_API_KEY'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $price_data = json_decode($response, true);

    // Extract the gold price from the response
    $gold_price = $price_data['price'];

    return $gold_price;
}

// get silver price
function get_silver_price()
{
    $url = 'https://www.goldapi.io/api/XAG/USD';
    $headers = array(
        'x-access-token: YOUR_API_KEY'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $price_data = json_decode($response, true);

    // Extract the gold price from the response
    $silver_price = $price_data['price'];

    return $silver_price;
}

// get platinum price
function get_platinum_price()
{
    $url = 'https://www.goldapi.io/api/XPT/USD';
    $headers = array(
        'x-access-token: YOUR_API_KEY'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $price_data = json_decode($response, true);

    // Extract the gold price from the response
    $platinum_price = $price_data['price'];

    return $platinum_price;
}
