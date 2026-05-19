# GA - Koddokumentation
## Noor Kheder

### CREATE - GET
```php
get('/create', function () {

    include("form.html");
});


```
Första delen är en GET-route som visar formulär.

***
### CREATE - POST
```php
session_start();
require("router.php");
require("response.php");
require("data.php");
require("html.php");


```
router.php: hanterar alla routes (get, post)
response.php: hanterar svar (JSON, HTML, redirect)
data.php: läser/skriver data (filsystem som databas)
html.php: helper-functions för HTML-element
***

### HOME
```php

get('/', function () {

    if (empty($_SESSION["user"])) {
        Res::render(El::h2("HOME") . El::a("plushies", "./plushies"));
        return;
    }

    Res::render(El::h2("welcome " . $_SESSION["user"]["name"]) . El::h2("HOME") . El::a("plushies", "./plushies"));
});



```
- if-satsen kontrollerar om användaren är inloggad via $_SESSION["user"]
- Om användaren inte är nloggad:
    - Visas en enkel hemsida med texten "HOME" 
- Om användaren är inloggad:
    - Visas ett välkomstmeddelande 
    - Användarens namn hämtas via  $_SESSION["user"]["name"]

***

### Hämta data
```php

get('/plushies', function () {

    $plushies = Data::getData("plushies");
    $html = [];

    foreach ($plushies as $p) {
        $id = $p['id'];
        $html[] = "<div id = $id >" .
            El::h2($p['brand']) .
            El::p($p['color']) .
            El::p($p["price"]) .
            El::a("delete", "./plushies/delete/$id") .
            El::a(
                "edit",
                "./plushies/edit/$id"
            );

    }

    res::render(join("", $html));
});


```
- Loopar igenom alla plushies och hämtar deras id
- Varje plushie omvandlas till HTML-strängar (t.ex: h2, p ,a)
- Sedan sparas datan i en ny array
***
### Create
```php

get('/plushies/$brand/$price/$color', function ($brand, $price, $color) {

    $plush = [

        "id" => uniqid(true),

        "brand" => $brand,
        "price" => (float)$price,
        "color" => $color


    ];
    $plushies = Data::getData("plushies");

    array_push($plushies, $plush);

    Data::saveData("plushies", $plushies);
});



```
- Skapar nya plushies med attributen som skickas in via URL:en (routen används för att skapa ett nytt plushie-objekt)
- Hämtar data från URL-parametrarna från variabler ($brand, $price, $color)
- Skapar ett nytt ID med hjälp av funktionen uniqid()
- Hämtar befintiga plushies från JSON-filen
- Lägger till det nya objektet i arrayen 
- Sparar den uppdaterade arrayen till JSON-filem 
***
### Spara data
```php

post('/save', function () {

    if (empty($_SESSION["user"])) {
        res::json(["error" => "not logged in"]);
        return;
    }

    $brand = $_POST['brand'] ?? "No_Brand";
    $price = $_POST['price'] ?? 0;
    $color = $_POST['color'] ?? "color unknown";


    $plush = [
        "id" => uniqid(true),
        "brand" => $brand,
        "price" => (float)$price,
        "color" => $color,
        "uid" => $_SESSION['user']['id'],
        "email" => $_SESSION['user']['email']

    ];
    $plushies = Data::getData("plushies");

    $plushies[] = $plush;

    Data::saveData("plushies", $plushies);



    res::json($plush);
});

```
- Tar in data från formuläret via $_POST-varaiabler 
- Om användaren inte skickar in data används en standardvärde (engivet efter "??")
- Datan sparas som attributen i en array som representerar ett objekt (plushie)
- If-satsen kontrollerar om användaren är inloggad och retunerar ett error meddelande om det finns ingen session 

***
### Delete
```php

get('/plushies/delete/$id', function ($id) {

    $plushies = Data::getData("plushies");



    $newplushies = [];
    $removeKey = -1;
    foreach ($plushies as $key => $plush) {
        if ($plush["id"] != $id) {
            $newplushies[] = $plush;
        } else {
            $removeKey = $key;
        }
    }

    if ($plushies[$removeKey]['uid'] == $_SESSION['user']['id']) {
        Data::saveData("plushies", $newplushies);
        Res::redirect("/plushies?deleted...");
        return;
    }
    Res::redirect("/plushies?forbidden");
});


```
- Hämtar data med getData()
- En ny array skapas ($newplushies)
- Foreach loop: loopar igenom objekten och hitar rätt ID
- If-satsen in i loopen: om objektets id inte matchar med den utvalda id, läggs den in i den nya arrayen 
- If-sats för session: Kontrollerar att användaren är på egen session annars kan inte hen ta bort eller uppdatera objektet
***
### Update
```php

post('/plushies/update/$id', function ($id) {


    $brand = trim($_POST['brand']);
    $price = trim($_POST['price']);
    $color = trim($_POST['color']);

    $plushies = Data::getData("plushies");

    $i = null;
    foreach ($plushies as $index => $plush) {
        if ($plush["id"] == $id) {
            $i = $index;
            break;
        }
    }

    if ($i === null) {
        res::redirect("/plushies?notfound");
        return;
    }
    if ($plushies[$i]['uid'] != $_SESSION['user']['id']) {

        res::redirect("/plushies?forbidden");
        return;
    }

    $plushies[$i]['brand'] = $brand ? $brand : $plushies[$i]['brand'];
    $plushies[$i]['price'] = $price ? $price : $plushies[$i]['price'];
    $plushies[$i]['color'] = $color ? $color : $plushies[$i]['color'];
    

    Data::saveData("plushies", $plushies);
    Res::redirect("/plushies?Updated... plats" . $i);
});



```
- Tar emot formuläret från användaren: 
    - Brand
    - Price
    - Color
- Hämtar data 
- Loopar igenom plushies för att hitta rätt plushie via ID
- $i = null: en variabel som sparar objektens position i arrayen 
- if-satsen för uid: kontrollerar att användaren är inne i rätt session genom user id (uid). Det mÅste vara rätt user id för att användaren ska ha behörighet att ändra objektet
- Ternary operator: om användern inte har fyllt in ett nytt värde då används den gamla


***
### Register
```php


get("/register", 'register');


post("/register", function () {

    $name = $_POST['name'] ? $_POST['name'] : "NO_NAME";

    if (empty($_POST['email']) || $_POST['email'] == null) {

        res::json(["error" => "no email"]);
        return;
    }

    if (empty($_POST['password']) || $_POST['password'] == null) {

        res::json(["error" => "password required"]);
        return;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];


    $password = password_hash($password, PASSWORD_DEFAULT);

    $users = Data::getData("users");


    if (!empty($users[$email])) {

        res::json(["error" => "user already exist"]);

        return;
    }

    $users[$email] = [

        "id" => uniqid(true),
        "email" => $email,
        "name" => $name,
        "password" => $password,


    ];


    Data::saveData("users", $users);
    header("location:./login?mes=register_success");
});



```
- Om name har inte fyllts in i formuläret äanvänds "NO_NAME" som standardvärde
- if-satsen för email och password: kontrollerar att email/password är fylld in i formuläret annars returneras error
- password_hash(): används för att password ska inte sparas i klartext
- if (!empty($users[$email])): kontrollerar att det blir inga dubbla emails registrerad 


***
### Login
```php

get("/login", 'login');
post("/login", function () {

    if (empty($_POST['email']) || $_POST['email'] == null) {

        res::json(["error" => "no email"]);
        return;
    }

    if (empty($_POST['password']) || $_POST['password'] == null) {

        res::json(["error" => "password required"]);
        return;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $users = Data::getData("users");

    if (empty($users[$email])) {

        res::json(["error" => "no user"]);
        return;
    }

    if (!password_verify($password, $users[$email]['password'])) {

        res::json(["error" => "incorrect password"]);
        return;
    }
    $session_user = [...$users[$email]];
    unset($session_user['password']);
    $_SESSION['user'] = $session_user;

    res::redirect("./");
});

get("/logout", function () {

    session_destroy();
    res::redirect("./");
});


```
- if (empty($_POST['email']): kollar om användaren har skrivit in email annars returneras error
- if (empty($_POST['password'])): kollar em password saknas annars returneras "password required"
- if (empty($users[$email])): Kollar om användaren finns annars behövs det registreras 
- if (!password_verify($password, $users[$email]['password'])): jämför password som har skrivits in med det hashade password som redan finns, annars returneras "incorrect password" om lösenordet inte stämmer 
- Kopierar user från databasen 
- Tar bort lösenord (säkerhet)
- Sparar user i session (loggar in)
- Skickar användaren till startsidan 
- session_destroy(); tar bort session 
- Användaren skickas tillbaka till startsidan som utloggad

***
