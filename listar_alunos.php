
<?php
// Inicia a sessão do sistema.
session_start();

// Verifica se o usuário está autenticado.
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Guarda o ID do usuário conectado.
$usuario_id = (int) $_SESSION["usuario_id"];

// Inclui a conexão com o banco de dados.
require_once "conexao.php";

// Prepara uma consulta para buscar somente os alunos
// pertencentes ao usuário que está conectado.
$sql = $conexao->prepare(
    "SELECT * FROM alunos
     WHERE usuario_id = ?
     ORDER BY nome ASC"
);

// Informa o ID do usuário à consulta.
$sql->bind_param("i", $usuario_id);

// Executa a consulta.
$sql->execute();

// Obtém os resultados encontrados.
$resultado = $sql->get_result();

// Conta quantos alunos pertencem ao usuário.
$total = $resultado->num_rows;
?>

<!-- Interface da listagem de alunos. -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Alunos | EduGestão</title>

    <!-- Importa o CSS principal do sistema. -->
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

<main class="page-shell">

    <!-- Cabeçalho do sistema. -->
    <header class="topbar">

        <div class="brand">
            <div class="brand-mark">✦</div>

            <div>
                EduGestão
                <small>Sistema de Gerenciamento de Alunos</small>
            </div>
        </div>

        <nav class="topbar-actions">
            <a class="btn-secondary" href="painel.php">
                ← Painel inicial
            </a>

            <a href="cadastrar_aluno.php">
                ＋ Novo aluno
            </a>
        </nav>

    </header>

    <!-- Título e quantidade de alunos cadastrados. -->
    <div class="page-heading">

        <div>
            <p class="eyebrow">GESTÃO ACADÊMICA</p>
            <h1>Alunos cadastrados</h1>
            <p>Consulte e gerencie seus registros.</p>
        </div>

        <div class="stat-card">
            <span>TOTAL DE ALUNOS</span>
            <strong><?php echo (int)$total; ?></strong>
        </div>

    </div>

    <!-- Verifica se existem alunos cadastrados. -->
    <?php if ($total > 0): ?>

        <div style="overflow-x:auto">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Data de nascimento</th>
                        <th>Turma</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <!-- Percorre os alunos encontrados na consulta. -->
                    <?php while ($aluno = $resultado->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo (int)$aluno["id"]; ?>
                            </td>

                            <td>
                                <strong style="color:#263750">
                                    <?php echo htmlspecialchars($aluno["nome"]); ?>
                                </strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($aluno["email"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($aluno["telefone"] ?? ""); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($aluno["data_nascimento"] ?? ""); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($aluno["turma"]); ?>
                            </td>

                            <!-- Links para editar ou excluir o aluno. -->
                            <td>

                                <a class="btn-small"
                                   href="editar_aluno.php?id=<?php echo (int)$aluno["id"]; ?>">
                                    Editar
                                </a>

                                <a class="btn-small btn-danger"
                                   href="excluir_aluno.php?id=<?php echo (int)$aluno["id"]; ?>">
                                    Excluir
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <!-- Mensagem exibida quando o usuário ainda não possui alunos. -->
        <div class="empty-state">

            <h3>Nenhum aluno cadastrado</h3>

            <p>
                Comece adicionando o primeiro aluno à sua conta.
            </p>

            <a href="cadastrar_aluno.php">
                Cadastrar aluno
            </a>

        </div>

    <?php endif; ?>

</main>

</body>
</html>

<?php
// Libera os recursos utilizados pela consulta.
$sql->close();
?>