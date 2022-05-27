<?php
    require('db/conexao.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crud de clientes</title>
    <style>
        table{
            border-collapse: collapse;
            width: 100%;
        }
        th,td{
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }
        p{
            padding:20px;
            border:1px solid #ccc;
        }
        .oculto{
            display:none;
        }
    </style>
</head>
<body>
    <h1>CRUD Clientes com PHP</h1>

    <form id="form_salva" method="post">
        <input type="text" name="nome" placeholder="Digite seu nome" required>
        <input type="email" name="email" placeholder="Digite seu email" required>
        <button type="submit" name="salvar">Salvar</button>
    </form>
   
    <form class="oculto" id="form_atualiza" method="post">
        <input type="hidden" id="id_editado" name="id_editado" placeholder="ID" required>
        <input type="text" id="nome_editado" name="nome_editado" placeholder="Editar seu nome" required>
        <input type="email" id="email_editado" name="email_editado" placeholder="Editar seu email" required>
        <button type="submit" name="atualizar">Atualizar</button>
        <button type="submit" id="cancelar" name="cancelar">Cancelar</button>
    </form>
    
    <form class="oculto" id="form_deleta" method="post">
        <input type="hidden" id="id_deleta" name="id_deleta" placeholder="ID" required>
        <input type="hidden" id="nome_deleta" name="nome_deleta" placeholder="Deletar seu nome" required>
        <input type="hidden" id="email_deleta" name="email_deleta" placeholder="Deletar seu email" required>
        <b>Tem certeza que quer deletar cliente <span id="cliente"></span>?</b>
        <button type="submit" name="deletar">Confirmar</button>
        <button type="submit" id="cancelar_delete" name="cancelar">Cancelar</button>
    </form>

<?php
    if (isset($_POST['salvar'])&& isset($_POST['nome'])&& isset($_POST['email'])){

        $nome = limparPost($_POST['nome']);
        $email = limparPost($_POST['email']);
        $data = date('d-m-Y');

        //VALIDAÇAO DE CAMPO VAZIO
        if ($nome =="" | $nome == null){
            echo "<b style='color:red'>Nome nao pode ser vazio</b>";
            exit();
        }
        if ($email =="" | $email == null){
            echo "<b style='color:red'>E-mail nao pode ser vazio</b>";
            exit();
        }
        // verificar se nome esta correto
        if (!preg_match("/^[a-zA-Z-' ]*$/",$nome)) {
            echo "<b style='color:red'>Somente permitido letras e espaços!</b>";
            exit();
        }
        //VRIFICAR SE E UM EMAIL VALIDO
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo  "<b style='color:red'>Invalido email formato</b>";
            exit();
        }

        $sql = $pdo -> prepare("INSERT INTO cliente VALUES (null,?,?,?)");
        $sql->execute(array($nome,$email,$data));
        echo "<b style='color:green'>Cliente Inserido com sucesso!</b>";
    }
?>

<?php
    if (isset($_POST['atualizar'])&& isset($_POST['id_editado'])&& isset($_POST['nome_editado'])&& isset($_POST['email_editado'])){
        $id = limparPost($_POST['id_editado']);
        $nome = limparPost($_POST['nome_editado']);
        $email = limparPost($_POST['email_editado']);

        //VALIDAÇAO DE CAMPO VAZIO
        if ($nome =="" | $nome == null){
            echo "<b style='color:red'>Nome nao pode ser vazio</b>";
            exit();
        }
        if ($email =="" | $email == null){
            echo "<b style='color:red'>E-mail nao pode ser vazio</b>";
            exit();
        }
        // verificar se nome esta correto
        if (!preg_match("/^[a-zA-Z-' ]*$/",$nome)) {
            echo "<b style='color:red'>Somente permitido letras e espaços!</b>";
            exit();
        }
        //VRIFICAR SE E UM EMAIL VALIDO
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo  "<b style='color:red'>Invalido email formato</b>";
            exit();
        }
        //  atualizar
        $sql = $pdo->prepare("UPDATE cliente SET nome=?, email=? WHERE id=?");
        $sql->execute(array($nome,$email,$id));
        echo "Atualizado ".$sql->rowCount()." registros!";
    }
?>

<?php
    // DELETAR DADOS
    if (isset($_POST['deletar'])&& isset($_POST['id_deleta'])&& isset($_POST['nome_deleta'])&& isset($_POST['email_deleta'])){
        $id = limparPost($_POST['id_deleta']);
        $nome = limparPost($_POST['nome_deleta']);
        $email = limparPost($_POST['email_deleta']);

    // deletar comando
    $sql = $pdo->prepare("DELETE FROM cliente WHERE id=? AND nome=? AND email=?");
    $sql->execute(array($id,$nome,$email));
    echo "Deletado com sucesso!";
    }
?>

<?php
    $sql = $pdo->prepare("SELECT * FROM cliente ORDER BY nome");
    $sql->execute();
    $dados = $sql->fetchAll();
?>

    <?php
        if(count($dados) > 0){
            echo "<br><br><table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>";

            foreach($dados as $chave => $valor){
                echo "
                <tr>
                    <td>".$valor['id']."</td>
                    <td>".$valor['nome']."</td>
                    <td>".$valor['email']."</td>
                    <td><a href='#' class='btn-atualizar' data-id='".$valor['id']."' data-nome='".$valor['nome']."' data-email='".$valor['email']."'>Atualizar</a> | 
                    <a href='#' class='btn-deletar' data-id='".$valor['id']."' data-nome='".$valor['nome']."' data-email='".$valor['email']."'>Deletar</a></td>
                </tr>";
            }
            echo "</table>";
        }else{
            echo "Nenhum cliente cadastrado";
        }
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(".btn-atualizar").click(function(){
            var id = $(this).attr('data-id');
            var nome =  $(this).attr('data-nome');
            var email = $(this).attr('data-email');

            $('#form_salva').addClass('oculto');
            $('#form_atualiza').removeClass('oculto');
            $('#form_deleta').addClass('oculto');

            $("#id_editado").val(id);
            $("#nome_editado").val(nome);
            $("#email_editado").val(email);
        });

        $(".btn-deletar").click(function(){
            var id = $(this).attr('data-id');
            var nome =  $(this).attr('data-nome');
            var email = $(this).attr('data-email');

            $("#id_deleta").val(id);
            $("#nome_deleta").val(nome);
            $("#email_deleta").val(email);
            $("#cliente").html(nome);

            $('#form_salva').addClass('oculto');
            $('#form_atualiza').addClass('oculto');
            $('#form_deleta').removeClass('oculto');
        });

        $('#cancelar').click(function(){
            $('#form_salva').removeClass('oculto');
            $('#form_atualiza').addClass('oculto');
        });
        $('#cancelar_delete').click(function(){
            $('#form_salva').removeClass('oculto');
            $('#form_atualiza').addClass('oculto');
            $('#form_deleta').addClass('oculto');
        });

    </script>
</body>
</html>