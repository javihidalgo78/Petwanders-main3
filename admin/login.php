<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form action="process_login.php" method="post">
            <div class="input-group">
                <input type="email" id="email" name="email" required placeholder="Email">
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" required placeholder="Password">
            </div>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>