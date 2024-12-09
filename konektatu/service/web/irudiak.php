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
    $uploadDir = '/var/www/html/uploads/';
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);

    if (!is_dir($uploadDir)) {
        echo "<p>El directorio 'uploads' no existe.</p>";
    }

    if (!is_writable($uploadDir)) {
        echo "<p>El directorio 'uploads' no tiene permisos de escritura.</p>";
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        echo "<p>El archivo ha sido subido correctamente: " . htmlspecialchars(basename($_FILES['file']['name'])) . "</p>";
    } else {
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
