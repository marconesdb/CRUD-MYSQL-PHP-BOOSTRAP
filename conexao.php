<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "cadastro_usuarios";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>