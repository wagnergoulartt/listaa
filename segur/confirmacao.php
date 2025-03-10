<html>
<head>
    <meta name="robots" content="noindex">
    <!-- Outras tags e metadados do cabeçalho -->
</head>
</html>


<?php
// Faz a conexão com o banco de dados
$conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");
// Verifica se a conexão foi bem sucedida
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}
// Obtém o valor do ID do convidado enviado pelo formulário
$id = $_POST["id"];
// Seleciona o convidado com o ID correspondente
$query = "SELECT * FROM convidados WHERE id = '$id'";
$result = mysqli_query($conexao, $query);
$row = mysqli_fetch_assoc($result);
// Inverte o status de confirmação do convidado
$presenca = !$row["presenca"];
// Atualiza o status do convidado no banco de dados
$query = "UPDATE convidados SET presenca = '$presenca' WHERE id = '$id'";
mysqli_query($conexao, $query);
// Fecha a conexão com o banco de dados
mysqli_close($conexao);
// Redireciona para a página anterior
header("Location: index.php");
exit();
?> 