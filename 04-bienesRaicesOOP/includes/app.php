<?php

require 'funciones.php';
require 'config/database.php';
require __DIR__ . '/../vendor/autoload.php';

//Conectarnos ala base de datos
$db = conectarDb();

use App\ActiveRecord;

ActiveRecord::setDB($db);

