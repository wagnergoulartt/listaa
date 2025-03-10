<?php
header('Content-Type: application/json');

$conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");

if (!$conexao) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Erro na conexão com o banco de dados'
    ]));
}

if (!isset($_POST['id'])) {
    die(json_encode([
        'status' => 'error',
        'message' => 'ID não fornecido'
    ]));
}

$id = intval($_POST['id']);

// Verifica se o convidado existe e está confirmado
$query = "SELECT * FROM convidados WHERE id = ? AND confirmado = 2";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Verifica se já está presente
    if ($row['presenca'] == 1) {
        die(json_encode([
            'status' => 'error',
            'message' => 'Presença já confirmada anteriormente'
        ]));
    }

    // Atualiza a presença
    $update = "UPDATE convidados SET presenca = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $update);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode([
            'status' => 'success',
            'nome' => $row['nome'],
            'message' => 'Presença confirmada com sucesso'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao confirmar presença'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Convidado não encontrado ou não confirmado'
    ]);
}

mysqli_close($conexao);
?>
