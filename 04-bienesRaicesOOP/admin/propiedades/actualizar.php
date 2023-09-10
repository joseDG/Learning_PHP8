<?php

use App\Propiedad;
use App\Vendedor;
use Intervention\Image\ImageManagerStatic as Image;

require '../../includes/app.php';

estaAutenticado();

// Validar la URL por ID valido.
$id =  $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id) {
    header('Location: /admin');
}


// Obtener la propiedad
$propiedad = Propiedad::find($id);

//Consultar para obtener todos los vendedores
$vendedores = Vendedor::all();

//Arreglo con mensajes de errores
$errores = Propiedad::getErrores();

//Ejecutar el codigo despues de que el usuario envia el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Asignar los atributos
    $args = $_POST['propiedad'];

    $propiedad->sincronizar($args);

    //Validacion
    $errores = $propiedad->validar();

    //Subida de archivos
    //Generar un nombre unico
    $nombreImagen =  md5( uniqid( rand(), true ) ) . ".jpg";

    if($_FILES['propiedad']['tmp_name']['imagen']){
        $imagen = Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800,600);
        $propiedad->setImagen($nombreImagen);
    }

    // El array de errores esta vacio
    if (empty($errores)) {
        // Almacenar la imagen
        if($_FILES['propiedad']['tmp_name']['imagen']) {
            $imagen->save(CARPETA_IMAGENES . $nombreImagen);
        }

        $propiedad->guardar();
    }
}

incluirTemplate('header');

?>

<h1 class="fw-300 centrar-texto">Administración - Editar Propiedad</h1>

<main class="contenedor seccion contenido-centrado">
    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST" enctype="multipart/form-data">
        <?php include '../../includes/templates/formulario_propiedades.php'; ?>
        
        <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
    </form>

</main>


<?php
    incluirTemplate('footer');
?>
