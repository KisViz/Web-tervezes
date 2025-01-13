<?php
session_start();
if(isset($_SESSION["user"])){
    header("location:../index.php");
}
$uzenet = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ((isset($_POST["felhasznalonev"]) || trim($_POST["felhasznalonev"])) && (isset($_POST["email"]) || trim($_POST["email"])) && (isset($_POST["jsz"]) || trim($_POST["jsz"])) && (isset($_POST["jszu"]) || trim($_POST["jszu"])) && (isset($_POST["orszag"]) || trim($_POST["orszag"])) && (isset($_POST["varos"]) || trim($_POST["varos"])) && (isset($_POST["iranyitoszam"]) || trim($_POST["iranyitoszam"])) && (isset($_POST["cim"]) || trim($_POST["cim"])) && (isset($_POST["telefonszam"]) || trim($_POST["telefonszam"]))) {
        $username = $_POST["felhasznalonev"];
        $email = $_POST["email"];
        $jsz = $_POST["jsz"];
        $jszu = $_POST["jszu"];
        $orszag = $_POST["orszag"];
        $varos = $_POST["varos"];
        $iranyitoszam = $_POST["iranyitoszam"];
        $cim = $_POST["cim"];
        $telefon = $_POST["telefonszam"];

        // Ellenőrzi, hogy a jelszó megfelel-e a követelményeknek
        function checkPassword($password) {
            return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password);
        }

        // Ellenőrzi, hogy az irányítószám és a telefonszám szám-e
        function isNumeric($value) {
            return is_numeric($value);
        }

        // Ellenőrzi, hogy az ország és a város csak szöveg
        function isText($value) {
            return ctype_alpha(str_replace(' ', '', $value));
        }


        // Ellenőrzi, hogy a felhasználónév már létezik-e a JSON fájlban
        function isUsernameAvailable($username) {
            $data = file_get_contents('../users/users.json');
            $users = json_decode($data, true);
            foreach ($users as $user) {
                if ($user['felhasznalonev'] === $username) {
                    return false;
                }
            }
            return true;
        }


        if (!checkPassword($jsz)) {
            $uzenet = 'A jelszónak legalább 8 karakter hosszúnak kell lennie és tartalmaznia kell legalább egy betűt és egy számot<br>';
        } elseif ($jsz !== $jszu && trim($jsz) !== trim($jszu)) {
            $uzenet = "A jelszavak nem egyeznek.<br>";
        } elseif (!isUsernameAvailable($username)) {
            $uzenet = "Ez a felhasználónév már foglalt.<br>";
        } elseif (!isNumeric($iranyitoszam) || !isNumeric($telefon)) {
            $uzenet = "Az irányítószám és a telefonszám csak számokat tartalmazhat.<br>";
        } elseif (!isText($username)) {
            $uzenet = 'A felhasználónév, az ország és a város csak szöveget tartalmazhat.<br>';
        } else {
            $hashedPassword = password_hash($jsz, PASSWORD_DEFAULT);

            $newUser = array(
                "felhasznalonev" => $username,
                "admin" => false,
                "orszag" => $orszag,
                "iranyitoszam" => $iranyitoszam,
                "varos" => $varos,
                "cim" => $cim,
                "email" => $email,
                "telefon" => $telefon,
                "jelszo" => $hashedPassword
            );

            $data = file_get_contents('../users/users.json');
            $users = json_decode($data, true);
            $users[] = $newUser;
            file_put_contents('../users/users.json', json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            header('location: login.php');
            exit;
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
    <title>Regisztárció</title>

    <link rel="stylesheet" href="../css/reg.css">
</head>
<body>

<!--fejlec-------------------------------------------------------------------------->

<header id="fejlec-header"> <!--fejlec-->
    <div id="nev"><a href="../index.php">MiniMoly</a></div>
    <nav id="fejlec-nav">
        <ul>
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

<!--oldal--------------------------------------------------------------------------------->


<div class = "container">

    <div class="cella" id="recept">

        <img src="../img/regkonyrecept.png" alt = "Könyv recept kép">

    </div>

    <div class="cella">
        <form action="reg.php" id="reg-form" method="post">
            <p id="form-mainLabel">Regisztráció</p>
            <label for="felhasznalonev">Felhasználónév:</label>                       <!--legyen a mezo erteke a beirt adat, ha ki van toltve-->
            <input id="felhasznalonev" type="text" name="felhasznalonev" required value="<?php if (isset($_POST['felhasznalonev'])) echo $_POST['felhasznalonev']; ?>">
            <label for="lakcim">Lakcím:</label>
            <input id="lakcim" type="text" name="orszag" placeholder="Ország" required value="<?php if (isset($_POST['orszag'])) echo $_POST['orszag']; ?>">
            <div class="lakcim-inputs">
                <input type="text" name="iranyitoszam" placeholder="Irányítószám" required value="<?php if (isset($_POST['iranyitoszam'])) echo $_POST['iranyitoszam']; ?>">
                <input type="text" name="varos" placeholder="Város" required value="<?php if (isset($_POST['varos'])) echo $_POST['varos']; ?>">
            </div>
            <input type="text" name="cim" placeholder="Utca, házszám" required value="<?php if (isset($_POST['cim'])) echo $_POST['cim']; ?>">
            <label for="email">E-mail cím:</label>
            <input id="email" type="email" name="email" required value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>">
            <label for="telefon">Telefonszám:</label>
            <input id="telefon" type="tel" name="telefonszam" required value="<?php if (isset($_POST['telefonszam'])) echo $_POST['telefonszam']; ?>">
            <label for="jsz">Jelszó:</label>
            <input id="jsz" type="password" name="jsz" required>
            <p id="jszleir">A jelszónak legalább 8 karakter hosszúnak kell lennie és tartalmaznia kell legalább egy betűt és egy számot!<br></p>
            <label for="jszu">Jelszó újra:</label>
            <input id="jszu" type="password" name="jszu" required>
            <input type="submit" value="Regisztáció">

            <p id="form-singIn">Van már
                <a id="roll" href="https://www.youtube.com/watch?v=xvFZjo5PgG0&pp=ygUPcmlja3JvbGwgbm8gYWRz" target="_blank">fiókod?</a>
                <a id="toLogin" href="login.php" target="_self">Jelentkezz be</a>!
            </p>

            <p class="hiba">
                <?php echo $uzenet?></p>


        </form>
    </div>

    <div class="cella" id="konyv">

        <img src="../img/regkonyv.webp" alt="Könyv kép">

    </div>

</div>


</body>
</html>
