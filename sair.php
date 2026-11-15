<?php
// Inicia ou recupera a sessão que será encerrada.
session_start();

// Limpa todas as variáveis armazenadas na sessão.
// Limpar as informacoes da sessao
$_SESSION = [];

// Apaga o cookie de sessão do navegador, quando utilizado.
// Apagar o cookie da sessao, se existir
if (ini_get("session.use_cookies")) {
    $dados = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 3600,
        $dados["path"],
        $dados["domain"],
        $dados["secure"],
        $dados["httponly"]
    );
}

// Destrói a sessão no servidor.
// Encerrar a sessao
session_destroy();

// Redireciona o usuário para a página de login.
// Voltar para a tela de login
header("Location: login.php");
exit;