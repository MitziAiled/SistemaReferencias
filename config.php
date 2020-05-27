<?php
require('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$cleardb_url      = parse_url(getenv("CLEARDB_DATABASE_URL"));
$cleardb_server   = $cleardb_url["host"];
$cleardb_username = $cleardb_url["user"];
$cleardb_password = $cleardb_url["pass"];
$cleardb_db       = substr($cleardb_url["path"],1);

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn'    => '',
    'hostname' => $cleardb_server,
    'username' => $cleardb_username,
    'password' => $cleardb_password,
    'database' => $cleardb_db,
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => ('ENVIRONMENT' !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);

try {
    $pdo = new PDO("mysql:host=". $cleardb_server . ";dbname=" . $cleardb_db, $cleardb_username, $cleardb_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: No se pudo hacer la conexión a la base de datos. " . $e->getMessage());
}
?>