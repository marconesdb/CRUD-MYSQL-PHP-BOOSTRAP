<?php
require_once "conexao.php";

$id = $_GET["id"];

$sql = "DELETE FROM pessoas WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    header("Location: index.php");
    exit;
} else {
    echo "Erro: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>