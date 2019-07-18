<?php
session_start(); //Inicialização da sessao 
//Memoria de Login entre todas as paginas

//Importando as configuraçoes de Banco de dados
require_once 'configDB.php';

//Limpando os dados de entrada POST
function verificar_entrada($entrada) {
    $saida = trim($entrada); //Remove espaço
    $saida = htmlspecialchars($saida); //remove html
    $saida = stripcslashes($saida); //Remove barras
    return $saida;
}

if (isset($_POST['action']) && $_POST['action'] == 'entrar') {
    //echo 'entrou';
    $nomeUsuario = verificar_entrada($_POST['nomeUsuario']);
    $senhaUsuario = verificar_entrada($_POST['senhaUsuario']);
    $senha = sha1($senhaUsuario);
//sha1 40Caracteres
    
    $sql = $conexão->prepare("SELECT * FROM usuario " . "WHERE nomeUsuario=? AND senha=?");
    $sql->bind_param("ss", $nomeUsuario, $senha);
    $sql->execute();

    $busca = $sql->fetch();
    
    if($busca != null){
        //usuario e senha estão corretos
       $_SESSION['nomeUsuario'] = $nomeUsuario;
       echo 'ok';
       
       if(!empty($_POST['lembrar'])){
           setcookie("nomeUsuario",$nomeUsuario, time()+(365*24*60*60));//1 ano de vida em segundos
           setcookie("senhaUsuario", $senhaUsuario, $senha, time()+(365*24*60*60));
           
       } else {
           //Limpa o cookie
           if(isset($_COOKIE["nomeUsuario"]))
               setcookie ("nomeUsuario",'');
          if(isset($_COOKIE["senhaUsuario"]))
               setcookie ("senhaUsuario",'');
           
       }
    }else    
        echo 'Falhou o login';

//sanitização de entradas POST
} elseif (isset($_POST['action']) && $_POST['action'] == 'registro') {
    $nomeCompleto = verificar_entrada($_POST['nomeCompleto']);
    $nomeUsuario = verificar_entrada($_POST['nomeUsuario']);
    $emailUsuario = verificar_entrada($_POST['emailUsuario']);
    $senhaUsuario = verificar_entrada($_POST['senhaUsuario']);
    $senhaUsuariConfirmar = verificar_entrada($_POST['senhaUsuarioConfirmar']);
    $criado = date("y-m-d H:i:s"); //cria uma data ano mes dia
    // Gerar um hassh para senhas
    $senha = sha1($senhaUsuario);
    $senhaConfirmar = sha1($senhaUsuariConfirmar);

    //echo 'Hash: ' . $senha;
    //echo $nomeCompleto ." " . $nomeUsuario . " " . $emailUsuario . " " . $senhaUsuario . " " . $senhaUsuariConfirmar . " " . $criado;
    // Conferancia de senha no Back end 
    if ($senha != $senhaConfirmar) {
        echo "as senhas nao Conferem  \n ";
        echo $senhaUsuario . "  " . $senhaUsuariConfirmar . "\n";
        echo $senha . "  " . $senhaConfirmar;
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
} else {
    header("location:index.php");
}