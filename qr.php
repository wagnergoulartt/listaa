<?php
// Conexão com o banco de dados
$conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Pega o ID da URL
$id = $_GET['id'];

// Busca os dados do convidado
$query = "SELECT * FROM convidados WHERE id = ?";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Aqui você pode gerar o QR code ou mostrar as informações do convidado
    // Por exemplo:
    echo "<h1>QR Code do Convidado</h1>";
    echo "<p>Nome: " . $row['nome'] . "</p>";
    // Adicione aqui a lógica para gerar o QR code
}

mysqli_close($conexao);
?>
