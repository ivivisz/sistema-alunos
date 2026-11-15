
<?php
$servidor = "127.0.0.1";
$usuario = "alunos_app";
$senha = "SUA_SENHA_AQUI";
$banco = "sistema_alunos";

try {
    $conexao = new mysqli(
        $servidor,
        $usuario,
        $senha,
        $banco,
        3306
    );

    $conexao->set_charset("utf8mb4");

} catch (mysqli_sql_exception $erro) {
    die("Erro ao conectar com o banco de dados.");
}
?>
