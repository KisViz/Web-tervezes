<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Kapcsolat</title>
    <link rel="stylesheet" href="../css/uzenetek.css">
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

<h1>Üzenetek:</h1>

<div class="kapcsolatok">
    <?php
    //a kapcsolat.json betoltese, adatok megjelenitese
    $data = file_get_contents('../users/kapcs.json');
    $kapcsolatok = json_decode($data, true);

    //a bajegyzesek megjelenitese listaelemenkent
    foreach ($kapcsolatok as $kapcsolat) {
        echo "<strong>Név:</strong> " . $kapcsolat['nev'] . "<br>";
        echo "<strong>E-mail:</strong> " . $kapcsolat['email'] . "<br>";
        echo "<strong>Telefonszám:</strong> " . $kapcsolat['telefon'] . "<br>";
        echo "<strong>Üzenet: <br> </strong> " . $kapcsolat['feedback'] . "<br>";

        //torles gomb letrehozsasa urlappal
        if (isAdminLoggedIn()) {
            echo '<form method="post" action="../funkciok/uziTorol.php">';
            //ez a mezo nem lathato, de a tovabbitashoz kell
            echo '<input type="hidden" name="tolosUziId" value="' . $kapcsolat['id'] . '">';
            echo '<input type="submit" value="Törlés">';
            echo '</form>';
        }

        echo '<hr>';
    }
    ?>
</div>

</body>
</html>
