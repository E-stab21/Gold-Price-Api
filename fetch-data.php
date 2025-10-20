<?php
// get prices (metal-api)
function get_prices_metal_api()
{
    // set API Endpoint and API key
    $endpoint = 'latest';
    $access_key = getenv('GOLD_PRICE_API_ACCESS_KEY');

    // Initialize CURL:
    $ch = curl_init('https://metals-api.com/api/'.$endpoint.'?access_key='.$access_key.'&base=USD&symbols=XAU,XAG,XPT,XPD');
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
    $palladium_rate = $exchangeRates['rates']['USDXPD'];

    return [$gold_rate, $silver_rate, $platinum_rate, $palladium_rate];
}

// get gold price (gold-api.com)
function get_gold_price() {
    // Initialize CURL:
    $ch = curl_init('https://api.gold-api.com/price/XAU');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Store the data:
    $json = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response:
    $exchangeRates = json_decode($json, true);

    return  $exchangeRates['price'];
}

// get gold price (gold-api.com)
function get_silver_price() {
    // Initialize CURL:
    $ch = curl_init('https://api.gold-api.com/price/XAG');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Store the data:
    $json = curl_exec($ch);
    curl_close($ch);

    // Decode JSON response:
    $exchangeRates = json_decode($json, true);

    return  $exchangeRates['price'];
}