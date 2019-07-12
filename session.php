<?php

session_start();
require_once 'configDB.php';
if (isset($_SESSION['nomeUsuario'])){
    $nomeUsuario = $_SESSION["nomeUsuario"];
    echo "Nome do Usuario $nomeUsuario";
}else    header ("location: index.php");
