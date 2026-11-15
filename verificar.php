<?php
// Inicia ou recupera a sessão do usuário.
session_start();
// Inclui a conexão com o banco de dados.
require_once "conexao.php";
// Impede acessar a verificação sem antes passar pelo login.
if (!isset($_SESSION["usuario_pendente"])) { header("Location: login.php"); exit; }
$mensagem = "";
// Processa o código quando o formulário é enviado.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = trim($_POST["codigo"] ?? "");
    $id = $_SESSION["usuario_pendente"];
    // Busca o código salvo e verifica se ainda está dentro do prazo.
$sql = $conexao->prepare("SELECT codigo_mfa, (expira_mfa > NOW()) AS valido FROM usuarios WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $usuario = $sql->get_result()->fetch_assoc();
    // Confere formato, existência, validade e igualdade do código informado.
if ($usuario && preg_match('/^[0-9]{6}$/', $codigo) && $usuario["codigo_mfa"] !== null && $usuario["valido"] == 1 && hash_equals($usuario["codigo_mfa"], $codigo)) {
        // Apaga o código usado para impedir que ele seja reutilizado.
$sql = $conexao->prepare("UPDATE usuarios SET codigo_mfa = NULL, expira_mfa = NULL WHERE id = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        session_regenerate_id(true);
        // Marca o usuário como autenticado após a validação do MFA.
$_SESSION["usuario_id"] = $id;
        unset($_SESSION["usuario_pendente"]);
        header("Location: painel.php");
        exit;
    } else { $mensagem = "Código inválido ou expirado. Faça login novamente para gerar outro código."; }
}
?>
<!-- Estrutura visual da tela de verificação em duas etapas. -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verificação | EduGestão</title><link rel="stylesheet" href="estilo.css">
</head>
<body>
<main class="auth-shell">
    <div class="auth-brand"><div class="brand"><div class="brand-mark">✦</div><div>EduGestão<small>Verificação de segurança</small></div></div></div>
    <section class="auth-card">
        <h1>Confirme seu acesso</h1>
        <p class="subtitle">Digite o código de 6 dígitos gerado para concluir seu login.</p>
        <!-- Envia o código de seis dígitos para validação neste arquivo. -->
<form method="POST">
            <label for="codigo">Código de verificação</label>
            <input id="codigo" type="text" name="codigo" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" placeholder="000000" autocomplete="one-time-code" required>
            <p class="helper-text">O código tem validade de 5 minutos.</p>
            <button type="submit">Verificar código →</button>
        </form>
        <?php if ($mensagem): ?><p class="message"><?php echo htmlspecialchars($mensagem); ?></p><?php endif; ?>
        <a class="back-link" href="login.php">← Voltar ao login</a>
    </section>
</main>
</body>
</html>
