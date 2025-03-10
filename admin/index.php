<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>LISTA DA SOCIAL (ADMIN)</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <meta name="robots" content="noindex">
    <style>
        .table-responsive { overflow-x: auto; }
        .btn-options {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 10px;
        }
        .btn-options .btn { flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <h2 class="text-center mb-4">Adicionar Convidado</h2>
                <form action="adicionar.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="documento">Documento:</label>
                        <input type="text" class="form-control" id="documento" name="documento" required maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="whatsapp">WhatsApp:</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" 
                               onkeyup="formatarTelefone(this)" maxlength="15">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Adicionar</button>
                    <a href="index.php" class="btn btn-primary btn-block mt-3">Início</a>
                </form>
            </div>
        </div>
        <hr>

        <!-- Campo de busca -->
        <div class="form-group">
            <input type="text" class="form-control" id="searchInput" placeholder="Digite para pesquisar...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Doc.</th>
                        <th>WhatsApp</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conexao = mysqli_connect("localhost", "u529068110_social24", "@Erick91492832", "u529068110_social24");

                    if (!$conexao) {
                        die("Falha na conexão: " . mysqli_connect_error());
                    }

                    $query = "SELECT id, nome, documento, whatsapp, confirmado FROM convidados ORDER BY nome ASC";
                    $result = mysqli_query($conexao, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $confirmado = $row["confirmado"] == 2 ? "Confirmado" : "Não Confirmado";
                            $btnClass = $row["confirmado"] == 2 ? "btn-success" : "btn-danger";
                            $whatsapp = !empty($row["whatsapp"]) ? $row["whatsapp"] : "Não cadastrado";
                            
                            echo "<tr class='guest-row'>";
                            echo "<td>" . $row["nome"] . "</td>";
                            echo "<td>" . $row["documento"] . "</td>";
                            echo "<td>" . $whatsapp . "</td>";
                            echo "<td></td>";
                            echo "</tr>";
                            echo "<tr class='guest-row-buttons'>";
                            echo "<td colspan='4'>";
                            echo "<div class='btn-options'>";
                            echo "<button class='btn $btnClass btn-sm toggle-confirm' data-id='" . $row["id"] . "'>$confirmado</button>";
                            echo "<a href='excluir.php?id=" . $row["id"] . "' class='btn btn-danger btn-sm'>Remover</a>";
                            echo "<a href='editar.php?id=" . $row["id"] . "' class='btn btn-primary btn-sm'>Editar</a>";
                            
                            // Botão de compartilhar com WhatsApp
                            if (!empty($row["whatsapp"])) {
                                $whatsappNumber = preg_replace("/[^0-9]/", "", $row["whatsapp"]);
                                $message = "Olá! Aqui está seu QR Code para a Social: https://socializando.com.br/qr.php?id=" . $row["id"];
                                echo "<a href='https://wa.me/55" . $whatsappNumber . "?text=" . urlencode($message) . "' target='_blank' class='btn btn-success btn-sm'>Compartilhar</a>";
                            }
                            
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Nenhum convidado cadastrado.</td></tr>";
                    }

                    mysqli_close($conexao);
                    ?>
                </tbody>
            </table>
        </div>
        
        <script>
            function formatarTelefone(input) {
                let value = input.value.replace(/\D/g, '');
                value = value.substring(0, 11);
                
                if (value.length > 2) {
                    value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
                }
                if (value.length > 9) {
                    value = value.substring(0, 9) + '-' + value.substring(9);
                }
                
                input.value = value;
            }

            $(document).ready(function() {
                $("#searchInput").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $(".guest-row").each(function(index) {
                        var row = $(this);
                        var nextRow = row.next('.guest-row-buttons');
                        var text = row.text().toLowerCase();
                        
                        if (text.indexOf(value) > -1) {
                            row.show();
                            nextRow.show();
                        } else {
                            row.hide();
                            nextRow.hide();
                        }
                    });
                });

                $('.toggle-confirm').click(function(e) {
                    e.preventDefault();
                    
                    var id = $(this).data('id');
                    var confirmado = $(this).text().trim() === 'Não Confirmado' ? 2 : 0;
                    
                    $.ajax({
                        type: 'POST',
                        url: 'atualizar_confirmacao.php',
                        data: { id: id, confirmado: confirmado },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                location.reload();
                            } else {
                                alert('Erro ao atualizar o status: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro ao enviar a solicitação AJAX:', error);
                        }
                    });
                });
            });
        </script>
    </div>
</body>
</html>
