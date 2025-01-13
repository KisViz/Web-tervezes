<?php
session_start();

$uzenet = []; //letrehozunk egy uzenet tombot, ebbe gyujtjuk a hibakat

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //ha a mezo nincs kitoltve, vagy whitespce levagas utan ures, akkor hozza adunk egy hibt a hiba tombhoz
    //azert === mert igy a tipust is ellenorzi, ha sima == lenne, akkor nem csak ures stringet enne meg
    if (!isset($_POST["nev"]) || trim($_POST["nev"]) === "") {
        $uzenet[] = "Add meg a neved!";
    }
    if (!isset($_POST["email"]) || trim($_POST["email"]) === "") {
        $uzenet[] = "Add meg az e-mail címed!";
    }
    if (!isset($_POST["telefon"]) || trim($_POST["telefon"]) === "") {
        $uzenet[] = "Add meg a telefonszámod!";
    }
    if (!isset($_POST["feedback"]) || trim($_POST["feedback"]) === "") {
        $uzenet[] = "Töltsd ki az üzenet mezőt!";
    }

    //valtozoknak ertekul adjuk a form adatait
    $nev = $_POST["nev"];
    $email = $_POST["email"];
    $telefon = $_POST["telefon"];
    $feedback = $_POST["feedback"];

    if (count($uzenet) == 0) { //ha nincs hiba betoltjuk a jsonfajlt
        $data = file_get_contents('../users/kapcs.json');
        $uzik = json_decode($data, true);

        // az utolso üzenet azonositojat meghatarozzuk
        if (count($uzik) > 0) {
            $id = $uzik[count($uzik) - 1]['id'] + 1;
            //az id legyen az eddigi uzenetek utolso id mezojenek erteke +1
        } else {
            $id = 1;
            //ha meg nincs uzenet, akkor ez legyen az elso
        }

        $newMessage = array(
            "id" => $id,
            "nev" => $nev,
            "email" => $email,
            "telefon" => $telefon,
            "feedback" => $feedback,
        );

        $uzik[] = $newMessage; //hozza fuzzuk az uj uzit a tobbihez
        file_put_contents('../users/kapcs.json', json_encode($uzik, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $siker = TRUE; //ha minden jo akkor sikeres volt

    } else {
        $siker = FALSE; //egyebkent nem:(
    }



}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Kapcsolat</title>
    <link rel="stylesheet" href="../css/kapcsolat.css">
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

<h1>Írj nekünk, vagy keresd fel az üzletünket!</h1>

<div class = "container">

    <div class="cella" id="telefon">

        <img src="../img/phone.png" alt="Telefonszám">
        <h2>+36/30 735 2463</h2>

    </div>

    <div class="cella">
        <form id="kapcsolat-form" method="post">

            <label id="form-mainLabel">Kapcsolat</label>
            <label for="nev1">Felhasználónév:</label>             <!--legyen a mezo erteke a beirt adat, ha ki van toltve-->
            <input id="nev1" type="text" name="nev" value="<?php if (isset($_POST['nev'])) echo $_POST['nev']; ?>"/>
            <label for="em">E-mail cím:</label>
            <input id="em" type="email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>"/>
            <label for="tel">Telefonszám:</label>
            <input id="tel" type="tel" name="telefon" value="<?php if (isset($_POST['telefon'])) echo $_POST['telefon']; ?>">
            <label for="uzi">Üzenet:</label>
            <textarea id="uzi" name="feedback" rows="5" cols="50" maxlength="200"
                      placeholder="Miben tudunk segíteni?"><?php if (isset($_POST['feedback'])) echo $_POST['feedback']; ?></textarea>

            <input type="submit" value="Üzenet küldése" name="oke">

            <?php
            if (isset($siker) && $siker === TRUE) {  // ha nem volt hiba, akkor a regisztráció sikeres
                echo "<p style='text-align: center'>Az üzenet sikeresen elküldve!</p>";
            } else {                                // ha vannak hiba uzenetek kiírjuk egy-egy bekezdésben
                foreach ($uzenet as $uzi) {
                    echo "<p class='hibak'>" . $uzi . "</p>";
                }
            }
            ?>

        </form>
    </div>

    <div class="cella" id="email">

        <img src="../img/email.webp" alt="Email cím">
        <h2>dorfuv42@gmail.com</h2>


    </div>

</div>

<div class="container">

    <div class="lenticella" id="terkep">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2758.9256545744656!2d20.14715972857732!3d46.25170575049449!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x474488718432ff3d%3A0x38989e1e324a412!2zU3plZ2VkLCBLw6Fyw6FzeiB1LiA3LCA2NzIw!5e0!3m2!1shu!2shu!4v1710325898475!5m2!1shu!2shu" width="500" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    <div class="lenticella" id="nyitva">
        <table>
            <thead>
            <tr>
                <th colspan="2">Nyitvatartás</th>
            </tr>
            </thead>
            <tr>
                <td>Hétfő</td>
                <td class="ido">8:00 - 19:00</td>
            </tr>
            <tr>
                <td>Kedd</td>
                <td class="ido">8:00 - 19:00</td>
            </tr>
            <tr>
                <td>Szerda</td>
                <td class="ido">8:00 - 19:00</td>
            </tr>
            <tr>
                <td>Csütörtök</td>
                <td class="ido">8:00 - 19:00</td>
            </tr>
            <tr>
                <td>Péntek</td>
                <td class="ido">8:00 - 19:00</td>
            </tr>
            <tr>
                <td>Szombat</td>
                <td class="ido">9:00 - 16:00</td>
            </tr>
            <tr>
                <td>Vasárnap</td>
                <td class="ido">Zárva</td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>