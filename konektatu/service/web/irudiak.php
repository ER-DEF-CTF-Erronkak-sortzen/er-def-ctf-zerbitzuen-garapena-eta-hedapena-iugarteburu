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
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['file']['name']);

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            echo "<p>Fitxategia igo da: " . htmlspecialchars(basename($_FILES['file']['name'])) . "</p>";
        } else {
            echo "<p>Errorea fitxategia igotzean.</p>";
        }
    }
    ?>
    <form enctype="multipart/form-data" method="POST">
        <input type="file" name="file" required>
        <button type="submit">Igo</button>
    </form>
</body>
</html>