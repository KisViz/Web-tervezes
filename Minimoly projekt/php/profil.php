<?php
session_start();
if(!isset($_SESSION["user"])){
    header("location:../index.php");
}

//json fájlból kinyer adatokat
function getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file) { //A mező neve, amelyet keresünk a JSON objektumokban; ezt hasonlítjuk az előzővel, ezt adjuk vissza, elérési út
    $fieldData = null;
    if (file_exists($json_file)) {
        $data = json_decode(file_get_contents($json_file), true);
        foreach ($data as $item) {
            if ($item[$fieldToFind] === $valueToFind) {
                $fieldData = $item[$fieldToExtract];
                break;
            }
        }
    }
    return $fieldData;
}

// Példa használat
$fieldToFind = "felhasznalonev"; // A keresett mező
$valueToFind = $_SESSION['user']['felhasznalonev']; // A felhasználó felhasználóneve
$fieldToExtract = "email"; // A kinyerni kívánt mező
$json_file = "../users/users.json"; // Az adatok JSON fájlja

$email = getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file);
$fieldToExtract = "telefon";
$telefon = getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file);
$fieldToExtract = "orszag";
$orszag = getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file);
$fieldToExtract = "iranyitoszam";
$iranyitoszam = getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file);
$fieldToExtract = "varos";
$varos = getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file);
$fieldToExtract = "cim";
$cim = getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file);
$fieldToExtract = "jelszo";
$jelszo = getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file);
$fieldToExtract="felhasznalonev";
$felhasznalonev=getFieldByField($fieldToFind, $valueToFind, $fieldToExtract, $json_file);

if (isset($_POST["szemelyes"])) {
    // Ellenőrizd, hogy a szükséges mezők léteznek-e a POST kérésben
    if (isset($_POST["email"]) && isset($_POST["telefon"]) && isset($_POST["orszag"]) &&
        isset($_POST["iranyitoszam"]) && isset($_POST["varos"]) && isset($_POST["cim"])) {
        // Az új adatok kiolvasása a POST kérésből
        $uj_email = $_POST["email"];
        $uj_telefon = $_POST["telefon"];
        $uj_orszag = $_POST["orszag"];
        $uj_iranyitoszam = $_POST["iranyitoszam"];
        $uj_varos = $_POST["varos"];
        $uj_cim = $_POST["cim"];

        // Az új adatok beállítása az adatbázisban
        $uj_adatok = array(
            "email" => $uj_email,
            "telefon" => $uj_telefon,
            "orszag" => $uj_orszag,
            "iranyitoszam" => $uj_iranyitoszam,
            "varos" => $uj_varos,
            "cim" => $uj_cim,
        );

        // Adatok frissítése az adatbázisban és a JSON fájlban
        if (updateUserByUsername($valueToFind, $uj_adatok, $json_file)) {
            // Frissített adatok beolvasása
            $email = $uj_email;
            $telefon = $uj_telefon;
            $orszag = $uj_orszag;
            $iranyitoszam = $uj_iranyitoszam;
            $varos = $uj_varos;
            $cim = $uj_cim;
        }
    } else {
        echo "Hiányzó adat(ok)!";
    }
}

// Adatok módosítása az adatbázisban és a JSON fájlban
function updateUserByUsername($username, $newData, $json_file) {
    if (file_exists($json_file)) {
        $data = json_decode(file_get_contents($json_file), true);
        foreach ($data as &$user) {
            if ($user["felhasznalonev"] === $username) {
                $user = array_merge($user, $newData);
                break;
            }
        }
        file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    return false;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["jelszomod"])) {
    if (isset($_POST["jsz"], $_POST["ujjelszo"], $_POST["ujjelszoell"])) {
        $regijelszo = $_POST["jsz"];
        $ujjelszofriss = $_POST["ujjelszo"];
        $ujjelszoellen = $_POST["ujjelszoell"];

        // Ellenőrzi, hogy a jelszó megfelel-e a követelményeknek
        function checkPassword($password) {
            return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password); //jelszó sor elején kezdődik, legalább egy kisbetű vagy nagybetű kell legyen benne,
        }                                            //következő karakterek között legalább egy szám
                                                                //legalább 8 karakter hosszú, sor vége
        if ($ujjelszofriss !== $ujjelszoellen) {
            $uzenet = "Az új jelszavak nem egyeznek.<br>";
        } else {
            // Új jelszó hashelése
            $ujjelszofriss = password_hash($ujjelszofriss, PASSWORD_DEFAULT);

            // Felhasználói adatok frissítése
            if (updateUser($_SESSION['user']['felhasznalonev'], ['jelszo' => $ujjelszofriss], '../users/users.json')) {
                $uzenet = "A jelszó sikeresen megváltozott.";
            } else {
                $uzenet = "Hiba történt a jelszó frissítése közben.";
            }
        }
    } else {
        $uzenet = "Hiányzó adat(ok)!";
    }
}

// Adatok módosítása az adatbázisban és a JSON fájlban
function updateUser($username, $newData, $json_file) {
    if (file_exists($json_file)) {
        $data = json_decode(file_get_contents($json_file), true);
        foreach ($data as &$user) {
            if ($user["felhasznalonev"] === $username) {
                $user = array_merge($user, $newData);
                break;
            }
        }
        file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    return false;
}



//profil törlése
if (isset($_POST["torol"])) {
    $usernameToDelete = $_SESSION["user"]["felhasznalonev"];
    if (deleteUser($usernameToDelete, "../users/users.json")) {
        header("Location: ../funkciok/logout.php");
    } else {
        echo "Hiba történt a felhasználó törlése közben.";
    }
}

// Felhasználó törlése az adatbázisból és a JSON fájlból
function deleteUser($username, $json_file) {
    if (file_exists($json_file)) {
        $data = json_decode(file_get_contents($json_file), true);
        $ujfajl=[];
        foreach ($data as $user) {
            if ($user["felhasznalonev"] !== $username) {
                $ujfajl[]=$user;
            }
        }
        file_put_contents($json_file, json_encode($ujfajl, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
    return false;
}





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="../css/profil.css">
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

<div class="tartalmaz">
    <h1>Személyes adatok</h1>
    <form id="personal-form" method="post">
        <label for="felhasznalonev">Felhasználónév: (<small>A felhasználónév nem módosítható</small>)</label><br>
        <input type="text" id="felhasznalonev" name="felhasznalonev" value="<?php echo $felhasznalonev ?>"><br>
        <label for="email">E-mail cím:</label><br>
        <input type="email" id="email" name="email" value="<?php echo $email ?>"><br>
        <label for="telefon">Telefonszám:</label><br>
        <input type="tel" id="telefon" name="telefon" value="<?php echo $telefon ?>"><br>
        <label for="orszag">Lakcím:</label><br>
        <input type="text" id="orszag" name="orszag" value="<?php echo $orszag ?>" placeholder="Ország"><br>
        <div class="lakcim-inputs">
            <input type="text" id="iranyitoszam" name="iranyitoszam" value="<?php echo $iranyitoszam ?>" placeholder="Irányítószám"><br>
            <input type="text" id="varos" name="varos" value="<?php echo $varos ?>" placeholder="Város"><br>
        </div>
        <input type="text" id="cim" name="cim" value="<?php echo $cim ?>" placeholder="Utca és házszám"><br>
        <button type="submit" name="szemelyes">Mentés</button>
    </form>

    <div class="jelszomodositas">
        <h1>Jelszó módosítása</h1>
        <form id="jelszoformazas" method="post">
            <label for="jsz">Jelenlegi jelszó:</label><br>
            <input type="password" id="jsz" name="jsz"><br>
            <label for="ujjelszo">Új jelszó:</label><br>
            <input type="password" id="ujjelszo" name="ujjelszo"><br>
            <label for="ujjelszoell">Új jelszó megerősítése:</label><br>
            <input type="password" id="ujjelszoell" name="ujjelszoell"><br>
            <button type="submit" name="jelszomod">Mentés</button>
            <button type="submit" name="torol">Profil törlése</button>
        </form>

    </div>
</div>


</body>
</html>
