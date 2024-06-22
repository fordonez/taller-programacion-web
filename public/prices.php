<?php

require_once 'config.php';

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');

echo json_encode(
    getPrice($_ENV['PRICES_API_KEY'], 'USD', 'PEN'),
    JSON_PRETTY_PRINT,
);
