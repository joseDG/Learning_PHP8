<?php

include '../../includes/funciones.php';
// Proteger esta ruta.

// $auth = estaAutenticado();
// if(!$auth) {
//     header('Location: /');
// }

require '../../includes/config/database.php';

$db = conectarDb();

$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

// Arreglo con mensaje de errores

$errores = [];

$titulo = '';
$precio = '';
$descripcion = '';
$habitaciones = '';
$wc = '';
$estacionamiento = '';
$vendedor = '';

//Ejecutar el codigo despues de eque el usuario envia el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";

    echo "<pre>";
    var_dump($_FILES);
    echo "</pre>";
    
    $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db, $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedor = mysqli_real_escape_string($db, $_POST['vendedorId']);
    $creado = date('Y/m/d');



    // filter var va a filtrar una variable
    // FILTER_VALIDATE_INT
    // FILTER_SANITIZE_INT

    // 

    $numero = "HOLA1";

    // Sanitizar va a hacer eso, limpiar los datos 
    $estacionamiento = filter_var($numero, FILTER_SANITIZE_NUMBER_INT);

    // Validar va a revisar que sea un tipo de dato valido.
    $estacionamiento = filter_var($numero, FILTER_VALIDATE_INT);


    // Existe otra opción llamada mysqli_real_escape_string, esta función va a eliminar los caracteres especiales o escaparlos para hacerlos compatibles con la base de datos.

    $titulo = mysqli_real_escape_string( $db, $_POST['titulo'] );

    // Todo esto de escapar datos y asegurarlos se puede evitar con Sentencias preparadas y PDO
    exit;
   

    $imagen = $_FILES['imagen'] ?? null;

    if (!$titulo) {
        $errores[] = 'Debes añadir un Titulo';
    }
    if (!$precio) {
        $errores[] = 'El Precio es Obligatorio';
    }
    if (strlen($descripcion) < 50) {
        $errores[] = 'La Descripción es obligatoria y debe tener al menos 50 caracteres';
    }
    if (!$habitaciones) {
        $errores[] = 'La Cantidad de Habitaciones es obligatoria';
    }
    if (!$wc) {
        $errores[] = 'La cantidad de WC es obligatoria';
    }
    if (!$estacionamiento) {
        $errores[] = 'La cantidad de lugares de estacionamiento es obligatoria';
    }
    if (!$vendedor) {
        $errores[] = 'Elige un vendedor';
    }

    if (!$imagen['name'] || $imagen['error']) {
        $errores[] = 'La imagen es obligatoria';
    }

    //Validar por tamano (1mb maximo)

    $medida = 2 * 1000 * 1000;
    

    if ($imagen['size'] > $medida) {
        $errores[] = 'La Imagen es muy grande';
    }


    // echo "<pre>";
    // var_dump($errores);
    // echo "</pre>";

    // El array de errores esta vacio
    if (empty($errores)) {

        // Subida de archivos

        //Crear carpeta
        $carpetaImagenes = '../../imagenes/';

        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        //Generar un nombre unico
        $nombreImagen =  md5(uniqid(rand(), true)) . ".jpg";

        // var_dump($imagen);

        move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );

        // Insertar en la BD.
        $query = " INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedores_id ) VALUES ( '$titulo', '$precio', '$nombreImagen', '$descripcion',  '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedor' )";


        $resultado = mysqli_query($db, $query);
        

        if ($resultado) {
            header('location: /admin/index.php?mensaje=1');
        }
    }

    // Insertar en la BD.


}


?>

<?php
$nombrePagina = 'Crear Propiedad';

incluirTemplate('header');
?>

<h1 class="fw-300 centrar-texto">Administración - Nueva Propiedad</h1>

<main class="contenedor seccion contenido-centrado">
    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
        <fieldset>
            <legend>Información General</legend>
            <label for="titulo">Titulo:</label>
            <input name="titulo" type="text" id="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio: </label>
            <input name="precio" type="number" id="precio" placeholder="Precio" value="<?php echo $precio; ?>">

            <label for="imagen">Imagen: </label>
            <input name="imagen" type="file" id="imagen" accept="image/jpeg, image/png">


            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion"><?php echo $descripcion; ?></textarea>

        </fieldset>


        <fieldset>
            <legend>Información Propiedad</legend>

            <label for="habitaciones">Habitaciones:</label>
            <input name="habitaciones" type="number" min="1" max="10" step="1" id="habitaciones" value="<?php echo $habitaciones; ?>">

            <label for="wc">Baños:</label>
            <input name="wc" type="number" min="1" max="10" step="1" id="wc" value="<?php echo $wc; ?>">

            <label for="estacionamiento">Estacionamiento:</label>
            <input name="estacionamiento" type="number" min="1" max="10" step="1" id="estacionamiento" value="<?php echo $estacionamiento; ?>">

            <legend>Información Vendedor:</legend>
            <label for="nombre_vendedor">Nombre:</label>

            <select name="vendedorId" id="nombre_vendedor">
                <option selected value="">-- Seleccione --</option>
                <?php while ($row = mysqli_fetch_assoc($resultado)) : ?>
                    <option <?php echo $vendedor === $row['id'] ? 'selected' : '' ?> value="<?php echo $row['id']; ?>"><?php echo $row['nombre'] . " " . $row['apellido']; ?>
                    <?php endwhile; ?>
            </select>
        </fieldset>

        <input type="submit" value="Crear Propiedad" class="boton boton-verde">

    </form>

</main>


<?php

incluirTemplate('footer');

mysqli_close($db); ?>

</html>