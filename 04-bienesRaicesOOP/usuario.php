<?php 

    // Consultar la propiedad
    require 'includes/app.php';
    $db = conectarDb();


    // Inserta un admin
    $email = "correo@correo.com";
    $password = "hola";

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
   
    //Query para crear el usuario
    $query = "INSERT INTO usuarios (email, password) VALUES('${email}', '${passwordHash}') ";

    // echo $query;

    mysqli_query($db, $query);


?>