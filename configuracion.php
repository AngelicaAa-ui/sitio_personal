<?php
    $conexion = new mysqli("sql203.infinityfree.com", "if0_40474186", "Pulgarcita878", "if0_40474186_sitioPersonaldb");
    if($conexion -> connect_error){
        die("Error de conexión: ". $conexion-> connect_error); 
    }                                                         
?>