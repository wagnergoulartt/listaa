<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/vendor/autoload.php');
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
?>
<!DOCTYPE html>
<html>
<head>
    <title>QR Code do Convidado</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
        .qr-container {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code {
            max-width: 300px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <?php
        if (!isset($_GET['id'])) {
            die("ID não fornecido");
        }

        $conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");

        if (!$conexao) {
            die("Falha na conexão: " . mysqli_connect_error());
        }

        $id = intval($_GET['id']);

        $query = "SELECT * FROM convidados WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='text-center'>";
            echo "<h1 class='mb-4'>QR Code do Convidado</h1>";
            echo "<p><strong>Nome:</strong> " . htmlspecialchars($row['nome']) . "</p>";
            echo "<p><strong>Documento:</strong> " . htmlspecialchars($row['documento']) . "</p>";
            
            // Configurações do QR Code alteradas para PNG
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'scale' => 10,
                'imageBase64' => true
            ]);

            // Cria o QR Code
            $qrcode = new QRCode($options);
            $qr_url = "https://socializando.com.br/qr.php?id=" . $row['id'];
            
            echo "<div class='qr-container'>";
            // Exibe o QR code como uma imagem PNG
            echo '<img src="' . $qrcode->render($qr_url) . '" alt="QR Code">';
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-danger'>Convidado não encontrado</div>";
        }

        mysqli_close($conexao);
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
