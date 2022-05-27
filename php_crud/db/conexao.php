<?php
// configuraçoes gerais
$servidor = "mysql:dbname=phpcrud";
$usuario = "root";
$senha = "";

//conexao
try{
    $pdo = new PDO($servidor,$usuario,$senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $erro){
    echo "Falha ao se conectar!".$erro->getMessage();
}

//(LIMPAR ENTRADAS)
function limparPost($dado){
$dado = trim($dado);
$dado = stripslashes($dado);
$dado = htmlspecialchars($dado);
return $dado;
}
?>