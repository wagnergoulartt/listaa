<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>LISTA DA SOCIAL (SEGURANÇA)</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <meta name="robots" content="noindex">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        .table-responsive { overflow-x: auto; }
        #reader { 
            width: 100%; 
            max-width: 500px; 
            margin: 0 auto; 
        }
        #result {
            margin-top: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert alert-info">
            <?php
            $conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");
            if (!$conexao) {
                die("Falha na conexão: " . mysqli_connect_error());
            }

            $query_quantidade_confirmados = "SELECT COUNT(*) AS quantidade_confirmados FROM convidados WHERE confirmado = 2";
            $result_quantidade_confirmados = mysqli_query($conexao, $query_quantidade_confirmados);
            $row_quantidade_confirmados = mysqli_fetch_assoc($result_quantidade_confirmados);
            $quantidade_confirmados = $row_quantidade_confirmados["quantidade_confirmados"];

            $query_quantidade_presenca = "SELECT COUNT(*) AS quantidade_presenca FROM convidados WHERE presenca = 1";
            $result_quantidade_presenca = mysqli_query($conexao, $query_quantidade_presenca);
            $row_quantidade_presenca = mysqli_fetch_assoc($result_quantidade_presenca);
            $quantidade_presenca = $row_quantidade_presenca["quantidade_presenca"];

            $quantidade_ausentes = $quantidade_confirmados - $quantidade_presenca;

            echo "Total de confirmados: " . $quantidade_confirmados . "<br>";
            echo "Convidados presentes: " . $quantidade_presenca . "<br>";
            echo "Convidados ausentes: " . $quantidade_ausentes;
            ?>
        </div>

        <!-- Seção do Leitor QR Code -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <div id="reader"></div>
                <div id="result" class="alert"></div>
            </div>
        </div>

        <!-- Campo de busca -->
        <div class="form-group">
            <input type="text" class="form-control" id="searchInput" placeholder="Digite para pesquisar...">
        </div>

        <div class="table-responsive">
            <form action="confirmacao.php" method="post">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>LISTA DE CONVIDADOS DA SOCIAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM convidados WHERE confirmado = 2 ORDER BY nome ASC";
                        $result = mysqli_query($conexao, $query);

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr class='guest-row'>";
                            echo "<td style='display: flex; align-items: center;'>";
                            echo "<div style='margin-left: 10px; flex-grow: 1;'>";
                            echo "<span style='color: " . ($row['cor'] == 'vermelho' ? 'red' : ($row['cor'] == 'verde' ? 'green' : ($row['cor'] == 'amarelo' ? 'yellow' : 'black'))) . ";'>" . $row["nome"] . "</span>";
                            echo "<br>";
                            echo "<span style='color: " . ($row['cor'] == 'vermelho' ? 'red' : ($row['cor'] == 'verde' ? 'green' : ($row['cor'] == 'amarelo' ? 'yellow' : 'black'))) . ";'>" . $row["documento"] . "</span>";

                            if (!empty($row["aniversariantes"])) {
                                echo "<br>";
                                echo "<span style='color: red; font-weight: bold;'>" . $row["aniversariantes"] . "</span>";
                            }
                            echo "</div>";

                            echo "<div>";
                            if ($row["presenca"]) {
                                echo "<button type=\"submit\" name=\"id\" value=\"" . $row["id"] . "\" class=\"btn btn-success\">Presente</button>";
                            } else {
                                echo "<button type=\"submit\" name=\"id\" value=\"" . $row["id"] . "\" class=\"btn btn-danger\">Confirmar Presença</button>";
                            }
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        mysqli_close($conexao);
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

<script>
    $(document).ready(function(){
        // Script para a busca em tempo real
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".guest-row").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Adiciona o botão de início do scanner
        $("#reader").before('<button id="startScanner" class="btn btn-primary mb-3">Ler QrCode</button>');
        
        // Configuração do leitor QR Code
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { 
                fps: 10,
                qrbox: {width: 250, height: 250},
                defaultDeviceId: 'environment',
                hideSelectCamera: true,
                hideSelectScanType: true,
                showTorchButtonIfSupported: false,
                showZoomSliderIfSupported: false,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ],
                rememberLastUsedCamera: true,
                aspectRatio: 1.0,
                text: {
                    camera: "Câmera",
                    scanningInProgress: "Escaneando...",
                    scanButtonStopScanningText: "Parar",
                    scanButtonStartScanningText: "Iniciar",
                    torchOnButton: "Ligar Flash",
                    torchOffButton: "Desligar Flash",
                    zoom: "Zoom",
                    chooseCamera: "Escolha a câmera",
                    selectCamera: "Selecionar Câmera",
                    cameraPermissionTitle: "Permissão para Câmera",
                    cameraPermissionRequesting: "Solicitando permissão para câmera...",
                    cameraPermissionRejectError: "Permissão para câmera não concedida",
                    cameraNotFound: "Câmera não encontrada",
                    scanningStatus: "Escaneando",
                    loadingScanEngine: "Carregando scanner..."
                }
            }
        );

        // Inicia o scanner apenas quando o botão for clicado
        $("#startScanner").click(function() {
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            $(this).hide(); // Esconde o botão após iniciar o scanner
        });

        function onScanSuccess(decodedText, decodedResult) {
            try {
                const url = new URL(decodedText);
                const id = url.searchParams.get('id');
                
                if (id) {
                    $.ajax({
                        url: 'confirmar_qr.php',
                        method: 'POST',
                        data: { id: id },
                        success: function(response) {
                            const data = JSON.parse(response);
                            const resultDiv = $('#result');
                            
                            if (data.status === 'success') {
                                resultDiv.removeClass('alert-danger').addClass('alert-success');
                                resultDiv.html(`Convidado confirmado: ${data.nome}`);
                                setTimeout(() => location.reload(), 2000);
                            } else {
                                resultDiv.removeClass('alert-success').addClass('alert-danger');
                                resultDiv.html(data.message);
                            }
                            resultDiv.show();
                        },
                        error: function() {
                            $('#result').removeClass('alert-success').addClass('alert-danger')
                                      .html('Erro ao processar o QR Code')
                                      .show();
                        }
                    });
                }
            } catch (error) {
                console.error('Erro ao processar QR Code:', error);
            }
        }

        function onScanFailure(error) {
            console.warn(`Erro na leitura do QR Code: ${error}`);
        }
    });
</script>
</body>
</html>
