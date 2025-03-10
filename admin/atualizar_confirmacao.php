<?php
// Faz a conexão com o banco de dados
$conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");

// Verifica se a conexão foi bem sucedida
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Obtém os dados enviados via AJAX
$id = $_POST['id'];
$confirmado = $_POST['confirmado'];

// Prepara a consulta usando prepared statement para evitar SQL Injection
$query = "UPDATE convidados SET confirmado = ? WHERE id = ?";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "ii", $confirmado, $id);

// Executa a consulta
if (mysqli_stmt_execute($stmt)) {
    // Retorna uma resposta em JSON
    echo json_encode(array("status" => "success"));
} else {
    echo json_encode(array("status" => "error", "message" => mysqli_error($conexao)));
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao);
?>
