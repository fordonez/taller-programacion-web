<?php

require_once 'config.php';

global $db;

// Imports de la librería JWT para decodificar el token
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;

// Validar que la petición venga desde POST, cualquier otro method debe cortar la ejecución
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['credential'])
    || empty(trim($_POST['credential']))
) {
    header('Location: /?error=invalid_request');
    exit();
}

// Obtener JWT token enviado por Google
$token = trim($_POST['credential']);

// Decodificar el JWT para obtener la data del usuario
try {
    $parser = new Parser(new JoseEncoder());
    $credentials = $parser->parse($token)->claims();
} catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
    error_log('Error decoding JWT: ' . $e->getMessage());
    header('Location: /?error=invalid_credentials');
    exit();
}

// Nombre de la tabla
$table = 'users';

// Filtro o 'where' para buscar usuario siempre por el ID de Google
$where = [
    'google_id' => $credentials->get('sub'),
];

// Data que posteriormente se va a persistir/actualizar en la base de datos
$data = [
    'first_name' => $credentials->get('given_name'),
    'last_name' => $credentials->get('family_name'),
    'email' => $credentials->get('email'),
    'picture' => $credentials->get('picture'),
    'google_id' => $credentials->get('sub'),
    'google_name' => $credentials->get('name'),
];

// Verificamos si el usuario ya existe
$user = $db->selectOne($table, $where);

// Si el usuario existe, entonces actualizamos la información desde Google
// caso contrario, creamos el usuario.
// Cuando el insert/update falla, se redirecciona al index indicando el error

if ($user) {
    if (!$db->update($table, $where, $data)) {
        header('Location: /?error=db_error');
        exit();
    }
} else {
    $data['password'] = randomString(); // Creamos una clave aleatoria

    if (!$db->insert($table, $data)) {
        header('Location: /?error=db_error');
        exit();
    }
}

// Almacenar los datos del usuario en la sesión
$_SESSION['user'] = $data;

// Redirigir al usuario a la página de éxito o al inicio
header('Location: /');
exit();
