<?php

require_once 'functions.php';
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;
use Database\MySQL;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$_host = $_ENV['DB_HOST'];
$_port = (int) $_ENV['DB_PORT'];
$_user = $_ENV['DB_USER'];
$_pass = $_ENV['DB_PASS'];
$_dbname = $_ENV['DB_NAME'];

$_google_client_id = $_ENV['GOOGLE_CLIENT_ID'];
$_google_client_secret = $_ENV['GOOGLE_CLIENT_SECRET'];

// Conectamos a la base de datos
$db = new MySQL($_host, $_port, $_user, $_pass, $_dbname);
$db->connect();

// Siempre inicializamos una sessión en el navegador
session_start();
