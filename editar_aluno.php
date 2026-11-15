
<?php
// Inicia a sessão do usuário.
session_start();

// Verifica se o usuário está autenticado.
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Guarda o ID do usuário que está conectado.
$usuario_id = (int) $_SESSION["usuario_id"];

// Inclui a conexão com o banco de dados.
require_once "conexao.php";

// Recebe e valida o ID do aluno enviado pela URL.
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: listar_alunos.php");
    exit;
}

// Busca o aluno somente se ele pertencer ao usuário conectado.
$sql = $conexao->prepare(
    "SELECT * FROM alunos WHERE id = ? AND usuario_id = ?"
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

$mensagem = "";

// Processa o formulário quando ele é enviado.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe os dados preenchidos.
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $data_nascimento = $_POST["data_nascimento"] ?? "";
    $turma = trim($_POST["turma"] ?? "");

    // Valida os campos obrigatórios.
    if (
        $nome == "" ||
        $turma == "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $mensagem = "Preencha os campos corretamente.";

    } else {

        // Transforma campos vazios em NULL.
        if ($telefone == "") {
            $telefone = null;
        }

        if ($data_nascimento == "") {
            $data_nascimento = null;
        }

        try {

            // Atualiza somente o aluno pertencente ao usuário conectado.
            $sql = $conexao->prepare(
                "UPDATE alunos
                 SET nome = ?, email = ?, telefone = ?,
                     data_nascimento = ?, turma = ?
                 WHERE id = ? AND usuario_id = ?"
            );

            $sql->bind_param(
                "sssssii",
                $nome,
                $email,
                $telefone,
                $data_nascimento,
                $turma,
                $id,
                $usuario_id
            );

            $sql->execute();

            // Retorna para a lista após salvar.
            header("Location: listar_alunos.php");
            exit;

        } catch (mysqli_sql_exception $erro) {

            // Exibe uma mensagem caso o e-mail já esteja cadastrado.
            $mensagem = $erro->getCode() == 1062
                ? "Este e-mail já pertence a outro aluno."
                : "Erro ao atualizar o aluno.";
        }
    }

    // Mantém os valores preenchidos caso ocorra algum erro.
    $aluno["nome"] = $nome;
    $aluno["email"] = $email;
    $aluno["telefone"] = $telefone;
    $aluno["data_nascimento"] = $data_nascimento;
    $aluno["turma"] = $turma;
}
?>
<!-- Formulário de edição preenchido com os dados atuais do aluno. -->
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Editar aluno | EduGestão</title><link rel="stylesheet" href="estilo.css"></head>
<body>
<main class="page-shell">
<header class="topbar"><div class="brand"><div class="brand-mark">✦</div><div>EduGestão<small>Sistema de Gerenciamento de Alunos</small></div></div><nav class="topbar-actions"><a class="btn-secondary" href="listar_alunos.php">← Lista de alunos</a></nav></header>
<div class="page-heading"><div><p class="eyebrow">GESTÃO ACADÊMICA</p><h1>Editar aluno</h1><p>Atualize os dados do cadastro selecionado.</p></div></div>
<section class="content-card">
<!-- Envia os novos dados para este mesmo arquivo. -->
<form method="POST" style="max-width:none;margin:0;padding:0;border:0;box-shadow:none">
<label for="nome">Nome completo *</label><input id="nome" type="text" name="nome" maxlength="120" required value="<?php echo htmlspecialchars($aluno["nome"], ENT_QUOTES, 'UTF-8'); ?>">
<label for="email">E-mail *</label><input id="email" type="email" name="email" maxlength="150" required value="<?php echo htmlspecialchars($aluno["email"], ENT_QUOTES, 'UTF-8'); ?>">
<label for="telefone">Telefone</label><input id="telefone" type="text" name="telefone" maxlength="30" value="<?php echo htmlspecialchars($aluno["telefone"] ?? "", ENT_QUOTES, 'UTF-8'); ?>">
<label for="data_nascimento">Data de nascimento</label><input id="data_nascimento" type="date" name="data_nascimento" value="<?php echo htmlspecialchars($aluno["data_nascimento"] ?? "", ENT_QUOTES, 'UTF-8'); ?>">
<label for="turma">Turma *</label><input id="turma" type="text" name="turma" maxlength="40" required value="<?php echo htmlspecialchars($aluno["turma"], ENT_QUOTES, 'UTF-8'); ?>">
<div class="form-actions"><button type="submit">Salvar alterações</button><a class="btn-secondary" href="listar_alunos.php">Cancelar</a></div>
<?php if ($mensagem): ?><p class="message"><?php echo htmlspecialchars($mensagem); ?></p><?php endif; ?>
</form>
</section>
</main>
</body>
</html>