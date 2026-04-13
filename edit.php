<?php

/* Vi är nu i edit.php och har ett id på en plushie vi vill editera

    Steg 1: hitta aktuell plushie
    Steg 2: använd datan i html-formuläret nedan

*/

$data = Data::getData("plushies");
$editPlushie ="";
foreach($data as $p){

    if($p['id'] == $id){
        $editPlushie = $p;
        break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPDATE</title>
</head>
<body>
        <h2>UPDATE</h2>
    <form action="../update/<?= $id ?>" method="post">
        <input type="text" name="brand" value = "<?= $p['brand'] ?>" placeholder="type">
        <input type="text" name="price" value = "<?= $p['price'] ?>"placeholder="price">
        <input type="text" name="color"value = "<?= $p['color'] ?>" placeholder="color">
        <button type = "submit">Save</button>
    </form>

</body>
</html>