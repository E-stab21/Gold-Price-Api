<?php
// get prices (metal-api)
function get_prices_metal_api()
{
    // set API Endpoint and API key
    $endpoint = 'latest';
    $access_key = 'API_KEY';

    // Initialize CURL:
    $ch = curl_init('https://metals-api.com/api/'.$endpoint.'?access_key='.$access_key.'&base=USD&symbols=XAU,XAG,XPT');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Store the data:
    $json = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response:
    $exchangeRates = json_decode($json, true);

    // Access the exchange rate values, e.g. GBP:
    $gold_rate = $exchangeRates['rates']['USDXAU'];
    $silver_rate = $exchangeRates['rates']['USDXAG'];
    $platinum_rate = $exchangeRates['rates']['USDXPT'];

    return [$gold_rate, $silver_rate, $platinum_rate];
}

// get gold price (gold-api.com)
function get_gold_price() {
    $common_markup = 10;

    // Initialize CURL:
    $ch = curl_init('https://api.gold-api.com/price/XAU');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Store the data:
    $json = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response:
    $exchangeRates = json_decode($json, true);

    return  $exchangeRates['price'] + $common_markup;
}

// get gold price (gold-api.com)
function get_silver_price() {
    $common_markup = 0.2;
    // Initialize CURL:
    $ch = curl_init('https://api.gold-api.com/price/XAG');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Store the data:
    $json = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response:
    $exchangeRates = json_decode($json, true);

    return  $exchangeRates['price'] + $common_markup;
}