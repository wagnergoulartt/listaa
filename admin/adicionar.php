<?php
$conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $documento = $_POST['documento'];
    $whatsapp = isset($_POST['whatsapp']) ? $_POST['whatsapp'] : ''; // Adiciona o campo whatsapp

    if (empty($nome) || empty($documento)) {
        echo "Por favor, preencha todos os campos.";
        die();
    }

    // Simplificando a query - removendo mes e tema que não são necessários
    $query = "INSERT INTO convidados (nome, documento, whatsapp) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conexao, $query);

    // Modificando o bind_param para incluir whatsapp
    mysqli_stmt_bind_param($stmt, "sss", $nome, $documento, $whatsapp);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "Convidado adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar convidado: " . mysqli_error($conexao);
        var_dump($_POST);
        var_dump($query);
        die();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexao);
    
    header('Location: index.php');
    exit();
}
?>
