<?php
// Faz a conexão com o banco de dados
$conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");

// Verifica se a conexão foi bem-sucedida
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $documento = $_POST['documento'];


    // Verifica se os campos obrigatórios estão preenchidos
    if (empty($nome) || empty($documento)) {
        echo "Por favor, preencha todos os campos.";
        die();
    }

    // Consulta para obter os valores de mes e tema do registro com o menor id
    $query_get_min_id_values = "SELECT mes, tema FROM convidados WHERE id = (SELECT MIN(id) FROM convidados)";
    $result_get_min_id_values = mysqli_query($conexao, $query_get_min_id_values);

    if ($result_get_min_id_values && mysqli_num_rows($result_get_min_id_values) > 0) {
        // Caso existam registros na tabela
        $row = mysqli_fetch_assoc($result_get_min_id_values);
        $mes = $row['mes'];
        $tema = $row['tema'];
    } else {
        // Caso a tabela esteja vazia, define mes e tema como NULL
        $mes = null;
        $tema = null;
    }

    // Prepara a query SQL usando instruções preparadas
    $query = "INSERT INTO convidados (nome, documento, mes, tema) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexao, $query);

    // Vincula os parâmetros e executa a query
    mysqli_stmt_bind_param($stmt, "ssss", $nome, $documento, $mes, $tema);
    $result = mysqli_stmt_execute($stmt);

    // Verifica se a query foi executada com sucesso
    if ($result) {
        echo "Convidado adicionado com sucesso!";
    } else {
        echo "Erro ao adicionar convidado: " . mysqli_error($conexao);
        // Adicione mais informações de debug, se necessário
        var_dump($_POST);
        var_dump($query);
        die(); // Interrompe a execução para evitar o redirecionamento em caso de erro
    }

    // Fecha a instrução preparada
    mysqli_stmt_close($stmt);
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao);

// Redireciona de volta para a página inicial
header('Location: index.php');
exit();
?>
