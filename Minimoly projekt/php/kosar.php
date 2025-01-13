<?php
session_start();


if (isset($_POST["rendeles"])){
if(count($_SESSION['kosar'])>=1) {
    $csomagolopapir = $_POST["csomagolopapir"];
    $szalag= $_POST["szalag"];

    $json = file_get_contents('../users/kosar.json');
    $adatok = json_decode($json, true);
foreach ($_SESSION['kosar'] as $kosar) {

    $adatok[] = array(
            'felhasznalo' => $_SESSION["user"]["felhasznalonev"],
            'cim' => $kosar["cim"],
            'csomagolopapir' => $csomagolopapir,
            'szalag' => $szalag,
    );

    }
    file_put_contents("../users/kosar.json", json_encode($adatok, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    unset( $_SESSION["kosar"]); //ürít
   header("Location:kosar.php");
   exit;
}
}
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Kosár</title>
    <link rel="stylesheet" href="../css/kosar.css">
</head>
<body>

<!--fejlec----------------------------------------------------------------------->

<header id="fejlec-header"> <!--fejlec-->
    <div id="nev"><a href="../index.php">MiniMoly</a></div>
    <nav id="fejlec-nav">
        <ul>

            <?php
            // megnezi, hogy a felh az admin e
            function isAdminLoggedIn() {
                return isset($_SESSION['user']) && $_SESSION['user']['admin'] === true;
            }

            // megszamolja a bejegyzeseket kapcsolat.json fajlban
            function bejegyzesSzaml() {
                $data = file_get_contents('../users/kapcs.json');
                $messages = json_decode($data, true);
                return count($messages);
            }

            //ha admin, akkor van gomb
            if (isAdminLoggedIn()) {
                echo '<li class="fejlec-nav-nyitos"><a href="uzenetek.php">' . bejegyzesSzaml() . ' db üzenet</a></li>';
            }
            ?>

            <li class="fejlec-nav-nyitos"><a href="../index.php">Kezdőlap</a></li>
            <li class="fejlec-nav-nyitos"><a href="konyvek.php">Könyvek</a></li>
            <li class="fejlec-nav-nyitos"><a href="kapcsolat.php">Kapcsolat</a></li>
            <?php
            if(isset($_SESSION["user"])){
                echo ' <li class="fejlec-nav-nyitos"><a href="../funkciok/logout.php">Kijelentkezés</a></li>
                     <li class="fejlec-nav-nyitos"><a href="profil.php">Profil</a></li>';
            }
            ?>
            <?php
            if(!isset($_SESSION["user"])){
                echo  '<li class="fejlec-nav-nyitos"><a href="login.php">Bejelentkezés</a></li>
                  <li class="fejlec-nav-nyitos"><a href="reg.php">Regisztráció</a></li>';
            }
            ?>
            <?php
            if(isset($_SESSION["user"])) { //ha be van jelntkezve
                // akkor ellenorizzuk hogy a koar letezik e sessionben
                if (!isset($_SESSION['kosar'])) {
                    $_SESSION['kosar'] = array(); // ha nem letrehozzuk
                }
                if (count($_SESSION['kosar']) < 1) { //ha tobb mint egy bejegyzes van benne, akkor a szammal irja ki a a kosarat
                    echo '<li class="fejlec-nav-kepes"><a href="kosar.php">
                    <img src="../img/minecart.webp" height="30" alt="Kosár">Kosár</a></li>';
                } else { //ha nincs bajagyzes akkor szam nelkül
                    echo '<li class="fejlec-nav-kepes"><a href="kosar.php">
                    <img src="../img/minecart.webp" height="30" alt="Kosár">Kosár (' . count($_SESSION['kosar']) . 'db termék)</a></li>';
                }
            } else { //ha nincs bejelentkezve, akkor is szam nelkül jeleniti meg
                echo '<li class="fejlec-nav-kepes"><a href="kosar.php">
                    <img src="../img/minecart.webp" height="30" alt="Kosár">Kosár</a></li>';
            }
            ?>


        </ul>
    </nav>
</header>

<!--oldal--------------------------------------------------------------------------->

<main>
    <div class="box">
        <h1>Kosár</h1>
        <div class="empty-cart">
            <?php
            if(!isset($_SESSION["user"])){ //ha nincs bejelentkezve, akkor atiranyitjuk a bej/reg oldalra
                echo '<p>Nem vagy bejelentkezve:(.<br><em>Jelentkezz be, vagy regisztálj a vásárlás folyatatásáshoz!</em></p>
                      <a href="login.php" class="button">Bejelentkezés</a>
                      <a href="reg.php" class="button">Regisztráció</a>';
            } else { //ha be vaj jelentkezve
                // akkor ellenorizzuk, hogy a kosar letezik-e a session-ben
                if (!isset($_SESSION['kosar'])) {
                    $_SESSION['kosar'] = array(); // ha nem letrehozzuk
                }

                if (count($_SESSION['kosar']) < 1) { //a nincs benne ele, akkor elkuldjuk vasarolni
                    echo '<p>A kosár jelenleg üres.<br><em>Ezt megváltoztathatod! Válassz termékeink közül!</em></p>
                      <a href="konyvek.php" class="button">Folytatom a vásárlást</a>';
                } else { //ha vannak elemek a kosarban

                    foreach ($_SESSION['kosar'] as $termek) { //akkor vegig megyunk a korar termekein
                        echo '<div class="konyvek">';

                        echo '<div class="kosar-kep">';
                        echo '<img src="' . $termek['kep'] . '" alt="1984">'; //megjelenitjuk a kepet
                        echo '</div>';

                        echo '<div class="kosar-nagy">';

                        echo '<div class="kosar-cim">';
                        echo $termek['cim']; //a cimet
                        echo '</div>';

                        echo '<div class="kosar-ar">';
                        echo $termek['ar'] . ' Ft'; //es az arat
                        echo '</div>';

                        echo '</div>';

                        echo '<div class="gombok">';


                        echo '<form method="post" action="../funkciok/kosarbolTorol.php">'; //troles gomb
                        echo '<input type="hidden" name="torlendoTemekId" value="' . $termek['id'] . '">'; //titokban atadjuk a term id-jet
                        echo '<input type="submit" class="kosar-torles" value="Könyv törlése">';
                        echo '</form>';

                        echo '</div>';

                        echo '</div>';

                        echo '<hr>';
                    }

                    echo '<form method="post" action="kosar.php">';

                    echo '<p>Ha ajándéknak szánod a könyvet válassz hozzá csomagolást!</p>'; //ha ajandeknek akarja, akkor tud valasztani
                    echo'
                            <select name="csomagolopapir">
                            <option selected>‎</option>
                            <option>Kék csomagolópapír</option>
                            <option>Zöld csomagolópapír</option>
                            <option>Rózsaszín csomagolópapír</option>
                            <option>Ezüst csomagolópapír</option>
                            <option>Arany csomagolópapír</option>
                        
                        </select>
                        <select name="szalag">
                            <option selected>‎</option>
                            <option>Kék szalag</option>
                            <option>Zöld szalag</option>
                            <option>Rózsaszín szalag</option>
                            <option>Ezüst szalag</option>
                            <option>Arany szalag</option>
                        </select>';
                    echo'<input class="rendeles" type="submit" name="rendeles" value="Rendelés">';
                    echo '</form>';


                }


            }
            ?>

        </div>
    </div>
</main>
</body>
</html>