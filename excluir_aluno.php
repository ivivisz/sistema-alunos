
<?php
// Inicia a sessão do usuário.
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

// Recebe e valida o ID do aluno pela URL.
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: listar_alunos.php");
    exit;
}

// Busca o aluno somente se ele pertencer ao usuário conectado.
$sql = $conexao->prepare(
    "SELECT id, nome, turma
     FROM alunos
     WHERE id = ? AND usuario_id = ?"
);

$sql->bind_param("ii", $id, $usuario_id);
$sql->execute();

$aluno = $sql->get_result()->fetch_assoc();

// Fecha a consulta após obter os dados.
$sql->close();

// Se o aluno não existir ou pertencer a outro usuário,
// retorna para a lista.
if (!$aluno) {
    header("Location: listar_alunos.php");
    exit;
}

// Só realiza a exclusão depois da confirmação.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Exclui somente o aluno pertencente ao usuário conectado.
    $sql = $conexao->prepare(
        "DELETE FROM alunos
         WHERE id = ? AND usuario_id = ?"
    );

    $sql->bind_param("ii", $id, $usuario_id);
    $sql->execute();

    // Retorna para a lista após excluir.
    header("Location: listar_alunos.php");
    exit;
}
?>

<!-- Tela de confirmação da exclusão. -->
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Excluir aluno | EduGestão</title><link rel="stylesheet" href="estilo.css"></head>
<body>
<main class="page-shell">
<header class="topbar"><div class="brand"><div class="brand-mark">✦</div><div>EduGestão<small>Sistema de Gerenciamento de Alunos</small></div></div><nav class="topbar-actions"><a class="btn-secondary" href="listar_alunos.php">← Lista de alunos</a></nav></header>
<section class="content-card danger-card">
<p class="eyebrow" style="color:#c2414b">ATENÇÃO</p><h1>Excluir cadastro</h1>
<p>Você está prestes a excluir este aluno. Essa ação não poderá ser desfeita.</p>
<p><strong>Nome:</strong> <?php echo htmlspecialchars($aluno["nome"], ENT_QUOTES, 'UTF-8'); ?></p>
<p><strong>Turma:</strong> <?php echo htmlspecialchars($aluno["turma"], ENT_QUOTES, 'UTF-8'); ?></p>
<!-- O envio deste formulário confirma a exclusão. -->
<form method="POST" style="margin:22px 0 0;padding:0;border:0;box-shadow:none">
<div class="form-actions"><button type="submit" style="background:#c2414b">Confirmar exclusão</button><a class="btn-secondary" href="listar_alunos.php">Cancelar</a></div>
</form>
</section>
</main>
</body>
</html>