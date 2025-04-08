<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Jelenléti Ív</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/scripts.js" defer></script>
</head>
<body>
    <!-- Belépési felület -->
    <div class="login-container">
        <h1>Belépés</h1>
        <form action="login.php" method="POST">
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Jelszó:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Belépés</button>
        </form>
    </div>
</body>
</html>
