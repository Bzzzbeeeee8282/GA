<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php include("authstyle.php")?>
</head>
<body>
    <main>
        <div class="resiter">
            <h2>login</h2>
            <form action="./login" method="post">
                <input type="email" name="email" placeholder="EMAIL">
                <input type="password" name="password" placeholder="PASSWORD">
                <input type="submit" value="LOGIN">
            </form>
        </div>
    </main>
</body>
</html>