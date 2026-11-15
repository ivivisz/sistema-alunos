
<?php
// Inicia a sessão do sistema.
session_start();

// Verifica se existe um usuário autenticado.
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Guarda o ID do usuário que está conectado.
$usuario_id = (int) $_SESSION["usuario_id"];

// Inclui a conexão com o MySQL.
require_once "conexao.php";

$mensagem = "";

// Executa o cadastro quando o formulário é enviado.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe os dados do formulário.
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $data_nascimento = $_POST["data_nascimento"] ?? "";
    $turma = trim($_POST["turma"] ?? "");

    // Verifica os campos obrigatórios.
    if ($nome == "" || $turma == "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = "Preencha os campos obrigatórios corretamente.";

    } else {

        // Converte campos opcionais vazios em NULL.
        if ($telefone == "") {
            $telefone = null;
        }

        if ($data_nascimento == "") {
            $data_nascimento = null;
        }

        try {

            // Insere os dados do aluno e o ID do usuário responsável.
            $sql = $conexao->prepare(
                "INSERT INTO alunos
                (nome, email, telefone, data_nascimento, turma, usuario_id)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            // Associa os valores aos parâmetros da consulta.
            $sql->bind_param(
                "sssssi",
                $nome,
                $email,
                $telefone,
                $data_nascimento,
                $turma,
                $usuario_id
            );

            // Executa o cadastro.
            $sql->execute();

            // Retorna para a listagem.
            header("Location: listar_alunos.php");
            exit;

        } catch (mysqli_sql_exception $erro) {

            // Trata erros de cadastro, como e-mail duplicado.
            $mensagem = $erro->getCode() == 1062
                ? "Este e-mail já está cadastrado."
                : "Não foi possível cadastrar o aluno.";
        }
    }
}
?>

<!-- Formulário visual de cadastro de alunos. -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cadastrar aluno | EduGestão</title>

    <!-- Importa o CSS principal. -->
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
        </nav>

    </header>

    <!-- Título da página. -->
    <div class="page-heading">
        <div>
            <p class="eyebrow">GESTÃO ACADÊMICA</p>
            <h1>Cadastrar aluno</h1>
            <p>Preencha as informações para adicionar um novo cadastro.</p>
        </div>
    </div>

    <section class="content-card">

        <!-- Formulário de cadastro. -->
        <form method="POST" style="max-width:none;margin:0;padding:0;border:0;box-shadow:none">

            <label for="nome">Nome completo *</label>
            <input
                id="nome"
                type="text"
                name="nome"
                maxlength="120"
                placeholder="Digite o nome completo"
                required
            >

            <label for="email">E-mail *</label>
            <input
                id="email"
                type="email"
                name="email"
                maxlength="150"
                placeholder="aluno@exemplo.com"
                required
            >

            <label for="telefone">Telefone</label>
            <input
                id="telefone"
                type="text"
                name="telefone"
                maxlength="30"
                placeholder="(92) 99999-9999"
            >

            <label for="data_nascimento">Data de nascimento</label>
            <input
                id="data_nascimento"
                type="date"
                name="data_nascimento"
            >

            <label for="turma">Turma *</label>
            <input
                id="turma"
                type="text"
                name="turma"
                maxlength="40"
                placeholder="Ex.: 6º período"
                required
            >

            <!-- Botões de ação do formulário. -->
            <div class="form-actions">
                <button type="submit">Cadastrar aluno</button>

                <a class="btn-secondary" href="painel.php">
                    Cancelar
                </a>
            </div>

            <!-- Exibe mensagens de validação. -->
            <?php if ($mensagem): ?>
                <p class="message">
                    <?php echo htmlspecialchars($mensagem); ?>
                </p>
            <?php endif; ?>

        </form>

    </section>

</main>

</body>
</html>