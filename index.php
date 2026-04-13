<?php
session_start();
require("router.php");
require("response.php");
require("data.php");
require("html.php");

get('/', function () {

    if (empty($_SESSION["user"])) {
        Res::render(El::h2("HOME") . El::a("plushies", "./plushies"));
        return;
    }

    Res::render(El::h2("welcome " . $_SESSION["user"]["name"]) . El::h2("HOME") . El::a("plushies", "./plushies"));
});

get('/plushies/edit/$id', "edit.php");


get('/plushies', function () {

    //Res::json(Data::getData("plushies"));
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

        //res::render(El::a("delete", "./plushies/delete/$id"));

        "</div>";
    }

    res::render(join("", $html));
});


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


get('/person/$name', function ($name) {

    echo ("HELLO $name");
});

get('/create', function () {

    include("form.html");
});



post('/save', function () {

    if (empty($_SESSION["user"])) {
        res::json(["error" => "not logged in"]);
        return;
    }

    //här hämtar vi data från form och skapar en ny plushie och sparar den i datanbase (filen)
    $brand = $_POST['brand'] ?? "No_Brand";
    $price = $_POST['price'] ?? 0;
    $color = $_POST['color'] ?? "color unknown";


    // DU måsta lägga in users email och id i en plush så att du vet vem som äger den...
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


//require: PHP
//include: HTML

//DELTE
get('/plushies/delete/$id', function ($id) {

    $plushies = Data::getData("plushies");
    // $plushID = $_GET['id']



    // Manuell filtrering
    // Skapa ny ny tom array
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

//UPDATE
post('/plushies/update/$id', function ($id) {


    $brand = trim($_POST['brand']);
    $price = trim($_POST['price']);
    $color = trim($_POST['color']);

    $plushies = Data::getData("plushies");

    $i = null;
    // Loopar igenom och hittar index (position ) och aktuell plush som skall ändras
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
    


        // Använder villkorsträng för att kolla om det nya har ett värde
    // om inte så använder vi det gamla...
    $plushies[$i]['brand'] = $brand ? $brand : $plushies[$i]['brand'];
    $plushies[$i]['price'] = $price ? $price : $plushies[$i]['price'];
    $plushies[$i]['color'] = $color ? $color : $plushies[$i]['color'];
    

    Data::saveData("plushies", $plushies);
    Res::redirect("/plushies?Updated... plats" . $i);
});

// REGISTER OCH LOGIN

//route som visar fil
get("/register", 'register');


//tar hand om register
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

    //HASH

    $password = password_hash($password, PASSWORD_DEFAULT);

    //spara users
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
