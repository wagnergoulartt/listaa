<?php
// Habilitar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carrega o autoload do Composer
require_once(__DIR__ . '/../vendor/autoload.php');

// Importa as classes do QRCode
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Convidado</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <?php
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Conexão com o banco de dados
                $conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");

                if (!$conexao) {
                    throw new Exception("Falha na conexão: " . mysqli_connect_error());
                }

                $nome = $_POST['nome'];
                $documento = $_POST['documento'];
                $whatsapp = isset($_POST['whatsapp']) ? $_POST['whatsapp'] : '';

                if (empty($nome) || empty($documento)) {
                    throw new Exception("Por favor, preencha todos os campos.");
                }

                // Remove caracteres não numéricos do WhatsApp
                $whatsapp = preg_replace("/[^0-9]/", "", $whatsapp);

                // Cria o diretório para QR Codes
                $qr_dir = __DIR__ . '/../qrcodes/';
                if (!file_exists($qr_dir)) {
                    if (!mkdir($qr_dir, 0777, true)) {
                        throw new Exception("Erro ao criar diretório para QR Codes");
                    }
                }

                // Insere o registro
                $query = "INSERT INTO convidados (nome, documento, whatsapp) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conexao, $query);
                mysqli_stmt_bind_param($stmt, "sss", $nome, $documento, $whatsapp);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Erro ao inserir registro: " . mysqli_error($conexao));
                }

                $id = mysqli_insert_id($conexao);

                // Configurações do QR Code
                $options = new QROptions([
                    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                    'eccLevel' => QRCode::ECC_L,
                    'scale' => 5
                ]);

                // Gera o QR Code
                $qrcode = new QRCode($options);
                $qr_url = "https://socializando.com.br/qr.php?id=" . $id;
                $qr_filename = 'qr_' . $id . '.png';
                $qr_path = $qr_dir . $qr_filename;
                
                // Salva o QR Code
                $qrcode->render($qr_url, $qr_path);

                // Atualiza o registro com o caminho do QR Code
                $qr_db_path = '../qrcodes/' . $qr_filename;
                $update_query = "UPDATE convidados SET qr_code = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($conexao, $update_query);
                mysqli_stmt_bind_param($update_stmt, "si", $qr_db_path, $id);
                mysqli_stmt_execute($update_stmt);

                echo "<div class='alert alert-success'>
                        Convidado adicionado com sucesso!<br>
                        <img src='{$qr_db_path}' width='150'><br>
                        <a href='index.php' class='btn btn-primary mt-3'>Voltar</a>
                      </div>";
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>
                    Erro: " . $e->getMessage() . "<br>
                    <a href='javascript:history.back()' class='btn btn-primary mt-3'>Voltar</a>
                  </div>";
        }
        ?>
