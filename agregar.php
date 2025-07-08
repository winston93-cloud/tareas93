<?php
    $nombrex = $_POST['nom'];
    $idex = $_POST['ide'];


    $nombrex = strtoupper($nombrex);
    $idex = strtoupper($idex);

// AQUI GUARDA LA CITA
//////////////////////////////////////////////////////////////////////////
$host = 'localhost';
$basededatos = 'winston_general';
$usuario = 'winston_richard';
$contraseña = '101605';

    $conexion = new mysqli($host, $usuario, $contraseña, $basededatos);
    $acentos = $conexion->query("SET NAMES 'utf8'");
    if ($conexion -> connect_errno)
        {
    die("Fallo la conexion:(".$conexion -> mysqli_connect_errno(). ")".$conexion-> mysqli_connect_error());
}
    mysql_connect("localhost","winston_richard","101605");
    mysql_select_db("winston_general");
  
    $query =("INSERT INTO ch_nombres (nombre,identificador) VALUES ('$nombrex','$idex')");
    $conexion->query($query);

    echo "¡Registro dado de Alta!";
   
?>