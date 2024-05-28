<?php
$servername = getenv('MYSQL_HOST') ?: 'viaduct.proxy.rlwy.net';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: 'HYeCOkhnNplYqtnsRlWfdVheopmmrOVvp';
$dbname = getenv('MYSQL_DATABASE') ?: 'railway';
$port = getenv('MYSQL_PORT') ?: 32801;

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to the database";
?>
