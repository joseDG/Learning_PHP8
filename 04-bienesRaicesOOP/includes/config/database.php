<?php

function conectarDb(): mysqli
{
    $db = new mysqli('localhost', 'root', 'admin', 'bienesraices_crud');

    if (!$db) {
        echo "Error no se pudo conectar";
        exit;
    }

    return $db;
}
