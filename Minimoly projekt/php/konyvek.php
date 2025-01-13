<?php
session_start();

// Ellenőrzi, hogy be van-e jelentkezve az admin felhasználó
function isAdminLoggedIn() {
    return isset($_SESSION['user']) && $_SESSION['user']['admin'] === true;
}

// Ellenőrzi, hogy a megadott felhasználó admin-e
function isAdmin($username) {
    $admins = json_decode(file_get_contents('../users/users.json'), true);
    foreach ($admins as $admin) {
        if ($admin['felhasznalonev'] === $username && $admin['admin'] === true) {
            return true;
        }
    }
    return false;
}

// A "Kosárba" gombra kattintva végrehajtott művelet
if (isset($_POST['add_to_cart'])) {


// Ellenőrizzük, hogy a kosár létezik-e a session-ben
    if (!isset($_SESSION['kosar'])) {
        $_SESSION['kosar'] = array(); // Ha nem, létrehozzuk
    }



    // az utolso üzenet azonositojat meghatarozzuk
    if (count($_SESSION['kosar']) > 0) {
        $id = $_SESSION['kosar'][count($_SESSION['kosar']) - 1]['id'] + 1;
        //az id legyen az eddigi uzenetek utolso id mezojenek erteke +1
    } else {
        $id = 1;
        //ha meg nincs uzenet, akkor ez legyen az elso
    }

    $book = array(
        'id' => $id,
        'cim' => $_POST['cim'],
        'ar' => $_POST['ar'],
        'kep' => $_POST['kep']
    );

    $_SESSION['kosar'][] = $book;

/*    $uzik[] = $book;
    addToCart($book);*/
    header('location: konyvek.php');
}


//admin funkció
$hibaUzenetek=[];

if (isset($_POST["hozzaadas"])) {
    if (
        isset($_POST['konyvcim']) &&
        isset($_POST['leirasSzoveg']) &&
        isset($_POST['armezo']) &&
        isset($_FILES['kepfeltoltes']) &&
        !empty($_POST["armezo"]) && !empty($_POST["konyvcim"]) && !empty($_POST["leirasSzoveg"]) && !empty($_FILES["kepfeltoltes"])
    ) {
        // Ellenőrizzük, hogy a fájl egy valós képfájl-e
        $fajlnev = $_FILES["kepfeltoltes"]["name"];  //a kép, amit feltöltöttünk az űrlapon, megkapja a nevét
        list($nev, $kiterjesztes_a) = explode(".", $fajlnev, 2);  // ez a fájlnév és a kiterjesztés szétválasztására szolgál, a . alapján szétszedi 2-re
        $kiterjesztes = strtolower($kiterjesztes_a);

        if ($kiterjesztes === "jpg" || $kiterjesztes === "png") {
            // Ellenőrizzük a fájl méretét maximum 10 MB
            if ($_FILES["kepfeltoltes"]["size"] > 10485760) {
                $hibaUzenetek = "A fájl túl nagy. Maximum 10 MB lehet.";
            } else {
                $json = file_get_contents('../users/konyvethozzaad.json');
                $konyvek = json_decode($json, true);

                $utvonal = "../img/konyvindex/" . $_FILES['kepfeltoltes']['name'];

                $belerak = array(
                    'cim' => $_POST['konyvcim'],
                    'leiras' => $_POST['leirasSzoveg'],
                    'ar' => $_POST['armezo'],
                    'kepeleresiutvonal' => $utvonal
                );

                $konyvek[] = $belerak;

                // Cél mappa, ahova a képeket menteni szeretnénk
                $targetDirectory = "../img/konyvindex/";

                // Teljes elérési útvonal a cél mappához
                $targetFile = $targetDirectory . basename($_FILES["kepfeltoltes"]["name"]);

                // Megpróbáljuk elmenteni a fájlt a cél mappába
                move_uploaded_file($_FILES["kepfeltoltes"]["tmp_name"], $targetFile);

                file_put_contents("../users/konyvethozzaad.json", json_encode($konyvek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                header('location: konyvek.php');
                exit;
            }
        }
    }

}




?>



<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Könyvek</title>

    <link rel="stylesheet" href="../css/konyvek.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<!--fejlec----------------------------------------------------------------------->

<header id="fejlec-header"> <!--fejlec-->
    <div id="nev"><a href="../index.php">MiniMoly</a></div>
    <nav id="fejlec-nav">
        <ul>

            <?php
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


<div class="container">
    <?php
    // Check if user is logged in
    if(!isset($_SESSION["user"])) {
        // If logged in, show the "Kosárba" button
        echo     '<br><div class="jelentkezzbe" ><strong>Jelentkezz be, hogy könyvet tudj vásárolni!</strong></div>';

    } ?>

    <div class="konyvek">
        <img src="../img/jokislanyokkezikonyvegyilkossághoz.jpg" alt="Jó kislányok kézikönyve gyilkossághoz">
        <div class="szoveg">
            <h1 class="iro">Holly Jackson - Jó kislányok kézikönyve gyilkossághoz</h1>
            <p class="leiras">
                Öt évvel ezelőtt Andie Bellt, a Little Kilton-i gimnázium diákját megölte a barátja. A rendőrség így tudja. A városban mindenki így tudja. A gyilkosság emléke azóta kísérti a kisváros lakóit, bár az élet látszólag nyugodt mederben folyik tovább. Pippa, a gimnázium egyik végzős diákja elhatározza, hogy egy iskolai projekt keretében előveszi az ügyet, és kideríti, mi történt valójában. Ő sosem hitte el, hogy Sal Singh a gyilkos. De ha nem ő az, akkor ki? És vajon meddig fogja tétlenül nézni, hogy Pippa egyre közelebb kerül a megdöbbentő igazsághoz? Kegyetlen társasjáték, ahol egy kisváros a játéktábla, és fogalmad sincs, ki nevet a végén...
            </p>


            <div class="ar">
                <form method="post" action="konyvek.php" class="ar">

                <?php
    // Check if user is logged in
    if(isset($_SESSION["user"])) {
        // If logged in, show the "Kosárba" button
        echo '
                <h2 class="ft">Ár: 4 749 Ft</h2>
                
                    <input type="hidden" name="cim" value="Holly Jackson - Jó kislányok kézikönyve gyilkossághoz">
                    <input type="hidden" name="ar" value="4 749">
                    <input type="hidden" name="kep" value="../img/jokislanyokkezikonyvegyilkossághoz.jpg">
                    <input type="submit" name="add_to_cart" value="Kosárba">
                    ';
    } ?>
                </form>
            </div>
        </div>
    </div>

    <div class="konyvek">
        <img src="../img/verity.jpg" alt="Verity">
        <div class="szoveg">
            <h1 class="iro">Colleen Hoover - Verity</h1>
            <p class="leiras">
                A küszködő, anyagi csőd szélén álló író, Lowen Ashleigh megkapja élete állásajánlatát. A bestsellerszerző Verity Crawford férje felkéri, hogy a balesetben megsérült író helyett megírja sikersorozatának befejező részeit. Lowen megérkezik a Crawford-házba, hogy átnézze Verity többévnyi jegyzeteit és vázlatait, remélve, hogy elegendő anyagot talál a munka elkezdéséhez. Ám az irodában nemcsak kaotikus állapotok fogadják, hanem egy önéletrajz is, amit az asszony a legnagyobb titokban írt. A kézirat minden oldala vérfagyasztó vallomást rejt, köztük annak az éjszakának a történetét is, mely örökre megváltoztatta a család életét. Lowen először úgy dönt, nem mutatja meg a kéziratot, mert annak tartalma még több fájdalmat okozna a gyászoló apának. De ahogy a férfi iránti érzelmei egyre erősebbé válnak, rájön, hogy talán mégis fel kéne fedni Verity mocskos titkait.
            </p>

            <div class="ar">
                <form method="post" action="konyvek.php" class="ar">
                    <?php
                    // Check if user is logged in
                    if(isset($_SESSION["user"])) {
                        // If logged in, show the "Kosárba" button
                        echo '<h2 class="ft">Ár: 3 514 Ft</h2>
                
                    <input type="hidden" name="cim" value="Colleen Hoover - Verity">
                    <input type="hidden" name="ar" value="3 514">
                    <input type="hidden" name="kep" value="../img/verity.jpg">
                    <input type="submit" name="add_to_cart" value="Kosárba">';
                    } ?>
                </form>
            </div>
        </div>
    </div>



    <div class="konyvek">
        <img src="../img/ketlepestavolsag.jpg" alt="Két lépés távolság">
        <div class="szoveg">
            <h1 class="iro">Rachael Lippincott - Két lépés távolság</h1>
            <p class="leiras">
                Stella Grant élete minden pillanatát pontosan megtervezi. Cisztás fibrózissal küzd, és egyedül egy új tüdő adhatna neki egy kicsit hosszabb, könnyebb életet. Mindig szigorúan három lépés távolságot kell tartania más betegektől, és ő nem olyan, aki kockáztatni merne. Egészen addig, amíg be nem toppan az életébe a vad Will Newman, akinek rakoncátlan tincseitől és csodás kék szemétől Stella gyomra azonnal szaltózni kezd. Azonban a fiú éppen az, akitől a lánynak mindenképp távol kéne tartania magát, hiszen már a lehelete is életveszélyes lehet a számára. De mi van, ha a szívük és zsigeri vágyódásuk egyre közelebb húzza őket egymáshoz? Ha csak egy kicsit lefaraghatnának a távolságból... Vajon két lépés tényleg olyan veszélyes lenne, ha egyszer csak így nem törik össze a szívük?
            </p>
            <div class="ar">
                <form method="post" action="konyvek.php" class="ar">
                    <?php
                    // Check if user is logged in
                    if(isset($_SESSION["user"])) {
                        // If logged in, show the "Kosárba" button
                        echo '<h2 class="ft">Ár: 3 009 Ft</h2>
                
                    <input type="hidden" name="cim" value="Rachael Lippincott - Két lépés távolság">
                    <input type="hidden" name="ar" value="3 009">
                    <input type="hidden" name="kep" value="../img/ketlepestavolsag.jpg">
                    <input type="submit" name="add_to_cart" value="Kosárba">
                   ';
                    } ?>
                </form>

            </div>
        </div>
    </div>


    <div class="konyvek">
        <img src="../img/elottemazelet.jpg" alt="Előttem az élet">
        <div class="szoveg">
            <h1 class="iro">Émile Ajar - Előttem az élet</h1>
            <p class="leiras">
                A regény főhőse egy arab kisfiú, Momo, aki a társadalom perifériájára szorult négerek, arabok, zsidók mozgalmas, de nélkülözésekkel teli életét éli. Szüleit nem ismeri, egy idős zsidó asszony, Rosa mama neveli, aki a hasonló sorsú gyerekek ellátásából tartja fenn magát. Momo hamar önállósághoz szokik e furcsa környezetben, s úgy segít magán, ahogy tud: lop, csal, vagányokkal és prostituáltakkal barátkozik, de Rosa mamához gyengéd szeretet fűzi. Hogy e nyomorgó, önmagának is hazudó, társadalmon kívüli réteg összetartása milyen erős, az Rosa mama életének utolsó hónapjaiban derül ki. Mindenki összefog, hogy a beteg öregasszony életének utolsó napjait széppé tegyék. Az igazi áldozatot Momo hozza: elkíséri Rosa mamát titkos pincéjébe, hogy az asszony ott fejezhesse be életét.
            </p>

            <div class="ar">
                <form method="post" action="konyvek.php" class="ar">
                    <?php
                    // Check if user is logged in
                    if(isset($_SESSION["user"])) {
                        // If logged in, show the "Kosárba" button
                        echo ' <h2 class="ft">Ár: 2 990 Ft</h2>
                
                    <input type="hidden" name="cim" value="Émile Ajar - Előttem az élet">
                    <input type="hidden" name="ar" value="2 990">
                    <input type="hidden" name="kep" value="../img/elottemazelet.jpg">
                    <input type="submit" name="add_to_cart" value="Kosárba">
                    ';
                    } ?>
                </form>

            </div>
        </div>
    </div>

    <div class="konyvek">
        <img src="../img/1984.jpg" alt="1984">
        <div class="szoveg">
            <h1 class="iro">George Orwell - 1984</h1>
            <p class="leiras">
                George Orwell zseniális disztópiáját 1948-ban írta meg, de mit sem veszített aktualitásából. Az író által bevezetett fogalmak, mint például a Nagy Testvér, a Gondolatrendőrség, a 101-es szoba, vagy az olyan mondatok, mint: "a szabadság az, ha szabadon kimondható, hogy kettő meg kettő az négy" a mai napig erősen hatnak.
                Egy olyan világ jelenik meg ebben a műben, ahol a rendszer legfőbb célja az emberek fölötti totális hatalom megszerzése legbelsőbb lényegük megtörésével, elméjük ízekre szedésével és teljes átalakításával. Ehhez minden létező eszközt bevetnek, a beszélt nyelv - és ezáltal a gondolatok - egyre erőteljesebb redukálásától kezdve az állandó megfigyelésen át (a Nagy Testvér mindent lát), az agymosáson, tudatmanipuláción, a legrafináltabb testi- és lelki kínzásokon keresztül a történelemhamisításig, míg végül: "Soha többé nem leszel képes normális emberi érzésekre. Minden meghal benned... Üres leszel. Kipréselünk belőled mindent, aztán megtöltünk önmagunkkal."
            </p>
            <div class="ar">
                <form method="post" action="konyvek.php" class="ar">
                    <?php
                    // Check if user is logged in
                    if(isset($_SESSION["user"])) {
                        // If logged in, show the "Kosárba" button
                        echo ' <h2 class="ft">Ár: 2 490 Ft</h2>
                
                    <input type="hidden" name="cim" value="George Orwell - 1984">
                    <input type="hidden" name="ar" value="2 490">
                    <input type="hidden" name="kep" value="../img/1984.jpg">
                    <input type="submit" name="add_to_cart" value="Kosárba">
                    ';
                    } ?>
                </form>
            </div>
        </div>
    </div>

    <div class="konyvek">
        <img src="../img/akaramazovtestverek.jpg" alt="A karamazov testvérek">
        <div class="szoveg">
            <h1 class="iro">Dosztojevszkij - A Karamazov testvérek</h1>
            <p class="leiras">
                Akárcsak a Bűn és bűnhődés vagy az Ördögök, Dosztojevszkij e legérettebb - s egész életművét betetőző - alkotása is egy valóságos bűntény elemeiből nőtt irodalmi remekké. Jellemeiben, történésében, filozófiájában mintegy összegződik az író teljes élettapasztalata: a páratlan pszichológiai hitelességgel motivált bűnügyi történet kibontása során Dosztojevszkij bölcseleti és művészi nézeteinek végső szintézisét fogalmazza meg. A Karamazov család tagjai: az apa és fiai az erjedő, felbomló múlt századi orosz társadalom sorsának hordozói. ,,A régi, a vad, a féktelen Oroszország elpusztítja önmagát - írja a mű alapeszméjéről Sőtér István -, de felnő egy új nemzedék, mely a jóság, a szeretet, az emberiség jegyében él majd, s begyógyítja a Karamazovok ütötte sebeket."
            </p>
            <div class="ar">
                <form method="post" action="konyvek.php" class="ar">
                    <?php
                    // Check if user is logged in
                    if(isset($_SESSION["user"])) {
                        // If logged in, show the "Kosárba" button
                        echo '<h2 class="ft">Ár: 7 990 Ft</h2>
                
                    <input type="hidden" name="cim" value="Dosztojevszkij - A Karamazov testvérek">
                    <input type="hidden" name="ar" value="7 990">
                    <input type="hidden" name="kep" value="../img/akaramazovtestverek.jpg">
                    <input type="submit" name="add_to_cart" value="Kosárba">
                    ';
                    } ?>
                </form>

            </div>
        </div>
    </div>

    <?php
            $data = file_get_contents('../users/konyvethozzaad.json');
            $megjelenit = json_decode($data, true);

            $szamol=0;
            foreach ($megjelenit as $key => $value) {
                if (is_array($value)) {  //Itt ellenőrizzük, hogy az aktuális $value egy tömb-e
                    // Ha igen, növeljük a számlálót
                    $szamol++;
                }
            }

    $konyvek = json_decode(file_get_contents('../users/konyvethozzaad.json'), true);
    if($szamol>=1){
            foreach ($megjelenit as $konyv){
                echo '    <div class="konyvek">';
                echo '<img src="' . $konyv['kepeleresiutvonal'] . '" alt="1984">'; //megjelenitjuk a kepet
                echo '<div class="szoveg">';
                echo ' <h1 class="iro">';
                echo  $konyv['cim'];
                echo '</h1>';
                echo '<p class="leiras">';
                echo  $konyv['leiras'];;
                echo '</p>
            <div class="ar">';
                echo '                <form method="post" action="konyvek.php" class="ar">';
                if(isset($_SESSION["user"])) {
                echo '<h2 class="ft">Ár: ';
                echo  number_format($konyv["ar"], 0, ".", " "); // space-szel szétválasztja a számot
                echo ' Ft</h2>';
                echo '
                    <input type="hidden" name="cim" value="' . $konyv["cim"] . '">
                    <input type="hidden" name="ar" value="' . $konyv["ar"] . '">
                    <input type="hidden" name="kep" value="' . $konyv["kepeleresiutvonal"] . '">
                    <input type="submit" name="add_to_cart" value="Kosárba">
                    ';}
                echo ' </form>
                    </div>
                </div>
                </div>';
            }
    } else if ($konyvek==null){
            echo'';
        }
    ?>
</div>



<?php
    if(isAdminLoggedIn()) {
        echo '<div class="tartalom">';
        echo '    <form method="POST" novalidate enctype="multipart/form-data">';
        echo '        <div id="konyvcime">';
        echo '            <label for="konyvcim">Könyv címe</label>';
        echo '            <br>';
        echo '            <input type="text" name="konyvcim" id="konyvcim" required><br>';
        echo '        </div>';

        echo '<div id="kep_feltoltes">
            <h3>Kép</h3>
            <label for="kepfeltoltes">';

        echo '
            </label>
            <input id="kepfeltoltes" type="file" name="kepfeltoltes" accept=".png, .jpg, .jpeg" required>
        </div>';


        echo '<div id="leiras">
            <label for="leirasSzoveg" >Leírás</label>
            <br>';
        echo '
            <textarea id="leirasSzoveg" name="leirasSzoveg" maxlength=1000 required>';
        echo '</textarea>';
        echo '</div>';


        echo '
        <div id="ar">
            <label for="armezo">Ár (Ft)</label>
            <br>';
        echo '
            <input id="armezo" type="number" name="armezo">';
        echo '</div>';

        echo '<div id="hozzaadasa">
            <input id="hozzaadas" type="submit" name="hozzaadas" value="Könyv hozzáadadás">

        </div>';

        echo '    </form>
                </div>';
    }
?>


</body>
</html>
