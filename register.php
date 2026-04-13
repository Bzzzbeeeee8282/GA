<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <?php include("authstyle.php")?>

</head>
<body>
    <main>
        <div class="register">
            <h2>REGISTER</h2>
            <form action="./register" method="post">
                <input type="name" name="name" placeholder="NAME">
                <input type="email" name="email" placeholder="EMAIL">
                <input type="password" name="password" placeholder="PASSWORD">
                <input type="submit" value="REGISTER">
            </form>
        </div>
    </main>
</body>
</html>