
<?php
// Inicia a sessão do sistema.
session_start();

// Inclui a conexão com o banco de dados.
require_once "conexao.php";

// Variáveis para mensagens na tela.
$mensagem = "";
$sucesso = false;

// Verifica se o formulário foi enviado.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe os dados preenchidos pelo usuário.
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $confirmar_senha = $_POST["confirmar_senha"] ?? "";

    // Verifica se todos os campos foram preenchidos.
    if ($nome == "" || $email == "" || $senha == "" || $confirmar_senha == "") {
        $mensagem = "Preencha todos os campos.";

    // Verifica se o e-mail possui formato válido.
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Digite um e-mail válido.";

    // Exige uma senha com pelo menos 8 caracteres.
    } elseif (strlen($senha) < 8) {
        $mensagem = "A senha deve ter pelo menos 8 caracteres.";

    // Verifica se as duas senhas são iguais.
    } elseif ($senha !== $confirmar_senha) {
        $mensagem = "As senhas não coincidem.";

    } else {

        // Verifica se já existe um usuário com esse e-mail.
        $consulta = $conexao->prepare(
            "SELECT id FROM usuarios WHERE email = ?"
        );

        $consulta->bind_param("s", $email);
        $consulta->execute();

        $usuario_existente = $consulta->get_result()->fetch_assoc();

        if ($usuario_existente) {
            $mensagem = "Este e-mail já está cadastrado.";

        } else {

            // Gera um hash seguro da senha.
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // Prepara a inserção do novo usuário.
            // Os campos MFA começam vazios e serão preenchidos no login.
            $cadastro = $conexao->prepare(
                "INSERT INTO usuarios
                (nome, email, senha, codigo_mfa, expira_mfa)
                VALUES (?, ?, ?, NULL, NULL)"
            );

            $cadastro->bind_param("sss", $nome, $email, $senha_hash);
// Executa o cadastro no banco de dados.
try {

    if ($cadastro->execute()) {
        $sucesso = true;
        $mensagem = "Usuário cadastrado com sucesso! Agora você pode entrar no sistema.";
    }

} catch (mysqli_sql_exception $erro) {

    // Trata a tentativa de cadastrar um e-mail já existente.
    if ($erro->getCode() == 1062) {
        $mensagem = "Este e-mail já está cadastrado.";
    } else {
        $mensagem = "Não foi possível cadastrar o usuário.";
    }
}

            $cadastro->close();
        }

        $consulta->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Criar conta | EduGestão</title>

    <!-- Importa o CSS principal do sistema. -->
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

<main class="auth-shell">

    <!-- Identificação visual do sistema. -->
    <div class="auth-brand">
        <div class="brand">
            <img src="assets/logo.png" alt="Logo do EduGestão" class="logo">
            <div>
                EduGestão
                <small>Sistema de Gerenciamento de Alunos</small>
            </div>
        </div>
    </div>

    <section class="auth-card">

        <h1>Criar uma conta</h1>

        <p class="subtitle">
            Preencha seus dados para acessar o sistema.
        </p>

        <?php if (!$sucesso): ?>

        <!-- Formulário de cadastro de usuário. -->
        <form method="POST">

            <label for="nome">Nome completo</label>
            <input
                type="text"
                id="nome"
                name="nome"
                placeholder="Digite seu nome"
                autocomplete="name"
                required
            >

            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="seuemail@exemplo.com"
                autocomplete="email"
                required
            >

            <label for="senha">Senha</label>
            <input
                type="password"
                id="senha"
                name="senha"
                placeholder="Crie uma senha"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <label for="confirmar_senha">Confirmar senha</label>
            <input
                type="password"
                id="confirmar_senha"
                name="confirmar_senha"
                placeholder="Digite a senha novamente"
                minlength="8"
                autocomplete="new-password"
                required
            >

            <button type="submit">Criar minha conta →</button>

        </form>

        <?php endif; ?>

        <!-- Exibe mensagens de sucesso ou erro. -->
        <?php if ($mensagem): ?>
            <p class="message">
                <?php echo htmlspecialchars($mensagem); ?>
            </p>
        <?php endif; ?>

        <!-- Link para retornar ao login. -->
        <a href="login.php" class="back-link">
            Voltar ao login
        </a>

    </section>

</main>

</body>
</html>