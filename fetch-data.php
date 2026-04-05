<?php
// get prices (PMXecute)
function get_prices_pmxecute() : array
{
    // set API Endpoint and API key
    $base_url = 'https://pmxconnect.demo.stonex.com/';
    $version = 'v1_1/';
    $endpoint = 'GetSpotRates/';
    $type = 'ALL';
    $access_key = 'API_KEY';

    // Initialize CURL:
    if (($ch = curl_init($base_url . $version . $endpoint . $type)) === false) {
        error_log("Curl Initialization Failed");
        return ['status' => false];
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'TokenID: ' . $access_key ));

    // Store the data:
    if (($json = curl_exec($ch)) === false) {
        error_log("Curl Exec Failed: " . curl_error($ch));
        return ['status' => false];
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Decode JSON response:
    $exchangeRates = json_decode($json, true);

    if ($http_code != 200) {
        return ['status' => $exchangeRates['status']];
    }

    // Access the exchange rate values, e.g. GBP:
    $gold_rate = 0;
    $silver_rate = 0;
    $platinum_rate = 0;
    $palladium_rate = 0;
    if (!empty($exchangeRates['result'])) {
        foreach ($exchangeRates['result'] as $item) {
            switch ($item['PAIR']) {
                case 'XAUUSD':
                    $gold_rate = $item['ASK'];
                    break;
                case 'XAGUSD':
                    $silver_rate = $item['ASK'];
                    break;
                case 'XPTUSD':
                    $platinum_rate = $item['ASK'];
                    break;
                case  'XPDUSD':
                    $palladium_rate = $item['ASK'];
            }
        }
    }

    return [
        'status' => 'success',
        'gold_price' => $gold_rate,
        'silver_price' => $silver_rate,
        'platinum_price' => $platinum_rate,
        'palladium_price' => $palladium_rate
    ];
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