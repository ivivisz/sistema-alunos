
<?php
// Inicia a sessão do usuário.
session_start();

// Verifica se o usuário está autenticado.
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Guarda o ID do usuário conectado.
$id = (int) $_SESSION["usuario_id"];

// Inclui a conexão com o banco de dados.
require_once "conexao.php";

// Busca o nome do usuário conectado.
$sql = $conexao->prepare(
    "SELECT nome FROM usuarios WHERE id = ?"
);

$sql->bind_param("i", $id);
$sql->execute();

$resultado = $sql->get_result();
$usuario = $resultado->fetch_assoc();

// Verifica se o usuário ainda existe no banco.
if (!$usuario) {
    unset($_SESSION["usuario_id"]);
    header("Location: login.php");
    exit;
}

// Conta somente os alunos cadastrados pelo usuário conectado.
$sql = $conexao->prepare(
    "SELECT
        COUNT(*) AS total,
        COUNT(DISTINCT turma) AS turmas
     FROM alunos
     WHERE usuario_id = ?"
);

$sql->bind_param("i", $id);
$sql->execute();

$dados = $sql->get_result()->fetch_assoc();

// Guarda os totais para exibir no painel.
$total_alunos = $dados["total"];
$total_turmas = $dados["turmas"];
?>

<!-- Interface do painel inicial do EduGestão. -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Painel | Sistema de Alunos</title>

    <link rel="stylesheet" href="estilo.css">

    <!-- Ícone da aba do navegador. -->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
</head>

<body>

<main class="page-shell">
    <!-- Cabeçalho com a identificação do sistema e os links principais. -->
<header class="topbar">
        <div class="brand">
    <img src="assets/logo.png" alt="Logo do EduGestão" class="logo">
    <div>
        EduGestão
        <small>Sistema de Gerenciamento de Alunos</small>
    </div>
</div>
        <nav class="topbar-actions"><a href="sair.php">Sair do sistema</a></nav>
    </header>
    <!-- Área de boas-vindas personalizada com o nome do usuário. -->
<section class="welcome-panel">
        <p class="eyebrow" style="color:#cbd7ff">PAINEL INICIAL</p>
        <h1>Olá, <?php echo htmlspecialchars($usuario["nome"]); ?>!</h1>
        <p>Bem-vindo ao seu espaço de gerenciamento acadêmico.</p>
    </section>
    <!-- Cartões com os indicadores gerais do sistema. -->
<section class="stats-grid">
        <article class="stat-card"><span>ALUNOS CADASTRADOS</span><strong><?php echo (int)$total_alunos; ?></strong></article>
        <article class="stat-card"><span>TURMAS REGISTRADAS</span><strong><?php echo (int)$total_turmas; ?></strong></article>
        <article class="stat-card"><span>STATUS DO SISTEMA</span><strong style="font-size:20px;color:#13845b">● Ativo</strong></article>
    </section>
    <section class="content-card">
        <div class="page-heading"><div><p class="eyebrow">GESTÃO ACADÊMICA</p><h2>O que deseja fazer?</h2><p>Escolha uma das opções para continuar.</p></div></div>
        <!-- Atalhos para cadastrar ou consultar alunos. -->
<div class="action-grid">
            <a class="action-card" href="cadastrar_aluno.php"><div class="action-icon">＋</div><strong>Cadastrar aluno</strong><span>Adicione um novo aluno ao sistema.</span></a>
            <a class="action-card" href="listar_alunos.php"><div class="action-icon">▤</div><strong>Consultar alunos</strong><span>Visualize, edite ou exclua cadastros existentes.</span></a>
        </div>
    </section>
</main>
</body>
</html>