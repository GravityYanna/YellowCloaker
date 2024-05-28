<?php
$servername = getenv('viaduct.proxy.rlwy.net');
$username = getenv('root');
$password = getenv('HYeCOkhNplYqtnsRIWfdVheopmmrOVvp');
$dbname = getenv('railway');
$port = getenv('32801');

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to the database";
?>
