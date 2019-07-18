<?php

session_start();
require_once 'configDB.php';
if (isset($_SESSION['nomeUsuario'])){
    $nomeUsuario = $_SESSION["nomeUsuario"];
   // echo "Nome do Usuario $nomeUsuario";
    $sql = $conexão->prepare("SELECT * FROM usuario WHERE " . "nomeusuario =  ?");
    $sql->bind_param("s",$nomeUsuario);
    $sql->execute();
    $resultado = $sql->get_result();
    $linha = $resultado->fetch_array(MYSQLI_ASSOC);
    
  //  $nomeUsuario = $linha["nomeUsuario"];
    $nomeCompleto = $linha["nome"];
    $email = $linha["email"];
    $criado = $linha["criado"];
}else    header ("location: index.php");
