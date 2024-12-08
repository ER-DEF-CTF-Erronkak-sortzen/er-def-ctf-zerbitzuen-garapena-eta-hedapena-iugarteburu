<!DOCTYPE html>
<html>
<head>
    <title>Argazki Igoera</title>
</head>
<body>
<header>
    <h1>Eibarko Margo Eskola</h1>
    <nav>
        <a href="index.php">Hasiera</a>
        <a href="ikastaroak.php">Ikastaroak</a>
        <a href="irudiak.php">Argazkiak</a>
    </nav>
</header>
<h1>Igo ikastaroetako argazkiak</h1>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ruta absoluta del directorio de uploads
    $uploadDir = '/var/www/html/uploads/';
    // Ruta completa del archivo cargado
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);

    // Mostrar detalles completos de los archivos cargados para depuración
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";

    // Comprobar si el directorio 'uploads' es accesible
    if (!is_dir($uploadDir)) {
        echo "<p>El directorio 'uploads' no existe.</p>";
    }

    // Comprobar si el directorio es escribible
    if (!is_writable($uploadDir)) {
        echo "<p>El directorio 'uploads' no tiene permisos de escritura.</p>";
    }

    // Intentar mover el archivo a la carpeta 'uploads'
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo "<p>El archivo ha sido subido correctamente: " . htmlspecialchars(basename($_FILES['file']['name'])) . "</p>";
    } else {
        // Mostrar ruta temporal y destino para depuración
        echo "<p>Error al mover el archivo. Detalles:</p>";
        echo "<ul>";
        echo "<li>Archivo temporal: " . $_FILES['file']['tmp_name'] . "</li>";
        echo "<li>Destino: " . $uploadFile . "</li>";
        echo "<li>Error de PHP: " . error_get_last()['message'] . "</li>";
        echo "</ul>";
    }
}
?>

<form enctype="multipart/form-data" method="POST">
    <input type="file" name="file" required>
    <button type="submit">Igo</button>
</form>
</body>
</html>
