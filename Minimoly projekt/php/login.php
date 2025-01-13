<?php
session_start();
$uzenet = "";
if(isset($_SESSION["user"])){
    header("location:../index.php");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ((isset($_POST["felhasznalonev"]) || trim($_POST["felhasznalonev"]) !== "") && (isset($_POST["jelszo"]) || trim($_POST["jelszo"]) !== "")) {
        $username = $_POST["felhasznalonev"];
        $password = $_POST["jelszo"];

        if (!empty($username) && !empty($password)) {
            $json_file = "../users/users.json";

            if (file_exists($json_file)) {
                $users = json_decode(file_get_contents($json_file), true);

                foreach ($users as $u) {
                    if ($u["felhasznalonev"] === $username && password_verify($password, $u["jelszo"])) {
                        $_SESSION["user"] = $u;
                        header("location:../index.php");
                    }
                }

                if (!isset($_SESSION["user"])) {
                    $uzenet = "Hibás felhasználónév vagy jelszó!";
                }
            } else {
                $uzenet = "Nincs regisztrált felhasználó!";
            }
        } else {
            $uzenet = "Kérlek töltsd ki mindkét mezőt!";
        }
    } else {
        $uzenet = "Hiányzó adat(ok)!";
    }
}
?>




<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>

    <link rel="stylesheet" href="../css/login.css">
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

<div class = "container">

    <div class="cella" id="konyv">

        <img src="../img/loginEnchantedBook.gif" alt = "Enchantolt könyv kép">

    </div>

    <div class="cella">
        <form id="reg-form" method="post" action="login.php">
            <p id="form-mainLabel">Bejelentkezés</p>
            <label for="felhasznalonev">Felhasználónév:</label>
            <input id="felhasznalonev" type="text" name="felhasznalonev" required value="<?php if (isset($_POST['felhasznalonev'])) echo $_POST['felhasznalonev']; ?>">
            <label for="jelszo">Jelszó:</label>
            <input id="jelszo" type="password" name="jelszo" required>
            <input type="submit" value="Bejelentkezés" name="login">

            <p id="form-login">Még nincs
                <a id="roll" href="https://www.youtube.com/watch?v=xvFZjo5PgG0&pp=ygUPcmlja3JvbGwgbm8gYWRz" target="_blank">fiókod?</a>
                <a id="toReg" href="reg.php" target="_self">Regisztrálj</a>
            </p>


            <p style="text-align: center"><?php echo $uzenet; ?></p>

        </form>
    </div>

</div>


</body>
</html>