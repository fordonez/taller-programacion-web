<?php

function randomString(int $length = 16): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $chars.= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars.= '0123456789';
    return substr(str_shuffle($chars), 0, $length);
}

function getPrice(string $apiKey, string $source, string $destination)
{
    $ch = curl_init('https://api.fastforex.io/fetch-one?from='.$source.'&to='.$destination.'&api_key='.$_ENV['PRICES_API_KEY']);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        error_log("cURL Error: $error");
        curl_close($ch);
        die('Error en la solicitud cURL: ' . $error);
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $error = json_last_error_msg();
        error_log("JSON Decode Error: $error");

        return ["message" => $error];
    }

    return [
        'price' => $data['result'][$destination],
        'timestamp' => (new DateTime())->format(DateTime::ATOM),
    ];
}
