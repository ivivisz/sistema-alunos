<?php
// Inicia a sessão para guardar o estado de autenticação do usuário.
session_start();
// Inclui a conexão com o banco de dados MySQL.
require_once "conexao.php";
// Guarda mensagens que serão exibidas na tela, como erro de login.
$mensagem = "";
// Executa este bloco quando o formulário de login é enviado.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Limpa qualquer autenticação ou verificação anterior.
    unset($_SESSION["usuario_id"]);
    unset($_SESSION["usuario_pendente"]);
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    // Consulta o usuário pelo e-mail usando uma instrução preparada.
$sql = $conexao->prepare("SELECT id, senha FROM usuarios WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $usuario = $sql->get_result()->fetch_assoc();
    // Confere se o usuário existe e se a senha corresponde ao hash salvo.
if ($usuario && password_verify($senha, $usuario["senha"])) {
        // Gera um código numérico de seis dígitos, incluindo zeros à esquerda se necessário.
$codigo = str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
        $id = $usuario["id"];
        // Salva o código MFA no banco e define validade de cinco minutos.
$sql = $conexao->prepare("UPDATE usuarios SET codigo_mfa = ?, expira_mfa = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE id = ?");
        $sql->bind_param("si", $codigo, $id);
        $sql->execute();
        session_regenerate_id(true);
        // Registra que a senha foi aceita, mas a verificação MFA ainda está pendente.
$_SESSION["usuario_pendente"] = $id;
        unset($_SESSION["usuario_id"]);
        header("Location: verificar.php");
        exit;
    } else { $mensagem = "E-mail ou senha incorretos."; }
}
?>
<!-- Estrutura visual da página de login. -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Entrar | EduGestão</title><link rel="stylesheet" href="estilo.css">
</head>
<body>
<main class="auth-shell">
    <div class="auth-brand"><div class="brand"><div class="brand-mark">✦</div><div>EduGestão<small>Sistema de Gerenciamento de Alunos</small></div></div></div>
    <section class="auth-card">
        <h1>Bem-vindo de volta</h1>
        <p class="subtitle">Entre com seus dados para acessar o sistema.</p>
        <!-- Formulário que envia e-mail e senha para este mesmo arquivo. -->
<form method="POST">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" placeholder="seuemail@exemplo.com" autocomplete="username" required>
            <label for="senha">Senha</label>
            <input id="senha" type="password" name="senha" placeholder="Digite sua senha" autocomplete="current-password" required>
            <button type="submit">Entrar no sistema →</button>
        </form>
        <!-- Exibe uma mensagem de erro somente quando houver conteúdo. -->
<?php if ($mensagem): ?><p class="message"><?php echo htmlspecialchars($mensagem); ?></p><?php endif; ?>
        <!-- Link para a tela de cadastro de novos usuários. -->
<a href="cadastro_usuario.php" class="back-link">Não tem uma conta? Cadastre-se</a>
    </section>
    <p style="text-align:center;font-size:12px;color:#8a96a8;margin-top:18px">Acesso restrito a usuários autorizados.</p>
</main>
</body>
</html>