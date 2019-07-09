<?php

//Importando as configuraçoes de Banco de dados
require_once 'configDB.php';

//Limpando os dados de entrada POST
function verificar_entrada($entrada) {
    $saida = trim($entrada); //Remove espaço
    $saida = htmlspecialchars($saida); //remove html
    $saida = stripcslashes($saida); //Remove barras
    return $saida;
}

if (isset($_POST['action']) && $_POST['action'] == 'registro') {
    $nomeCompleto = verificar_entrada($_POST['nomeCompleto']);
    $nomeUsuario = verificar_entrada($_POST['nomeUsuario']);
    $emailUsuario = verificar_entrada($_POST['emailUsuario']);
    $senhaUsuario = verificar_entrada($_POST['senhaUsuario']);
    $senhaUsuariConfirmar = verificar_entrada($_POST['senhaUsuarioConfirmar']);
    $criado = date("y-m-d"); //cria uma data ano mes dia
    // Gerar um hassh para senhas
    $senha = sha1($senhaUsuario);
    $senhaConfirmar = sha1($senhaUsuariConfirmar);

    //echo 'Hash: ' . $senha;
    //echo $nomeCompleto ." " . $nomeUsuario . " " . $emailUsuario . " " . $senhaUsuario . " " . $senhaUsuariConfirmar . " " . $criado;
    // Conferancia de senha no Back end 
    if ($senha != $senhaConfirmar) {
        echo "as senhas nao Conferem  \n ";
        echo $senhaUsuario ."  " . $senhaUsuariConfirmar ."\n";
        echo $senha ."  " . $senhaConfirmar;
    } else {
        //Verificando se o usuario existe no Banco de dados
        //Usando MySQl prepared statment
        $sql = $conexão->prepare("SELECT nomeUsuario, email FROM " . "usuario WHERE nomeUsuario = ? OR " . "email = ?");
        $sql->bind_param("ss", $nomeUsuario, $emailUsuario);
        $sql->execute();
        $resultado = $sql->get_result();
        $linha = $resultado->fetch_array(MYSQLI_ASSOC);
        if ($linha['nomeUsuario'] == $nomeUsuario)
            echo "Nome {$nomeUsuario} indisponivel";
        else if ($linha['email'] == $emailUsuario)
            echo "E mail {$emailUsuario} indisponivel";
        else {
            // preparar a inserçao no banco de dados
            $sql = $conexão->prepare("INSERT INTO usuario " . "(nome, nomeUsuario, email, senha, criado) " . "VALUE (?, ?, ?, ?, ?)");
            $sql->bind_param("sssss", $nomeCompleto, $nomeUsuario, $emailUsuario, $senha, $criado);
            if ($sql->execute()) {
                echo 'Cadastrado com sucesso!';
            } else {
                echo 'Algo deu errado. por favor tente novamente. ';
            }
        }
    }
}