<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Kezdőlap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/index.css">
</head>
<body>

<!--fejlec----------------------------------------------------------------------->

<header id="fejlec-header"> <!--fejlec-->
    <div id="nev"><a href="index.php">MiniMoly</a></div>
    <nav id="fejlec-nav">
        <ul>

            <?php
            // megnezi, hogy a felh az admin e
            function isAdminLoggedIn() {
                return isset($_SESSION['user']) && $_SESSION['user']['admin'] === true;
            }

            // megszamolja a bejegyzeseket kapcsolat.json fajlban
            function bejegyzesSzaml() {
                $data = file_get_contents('users/kapcs.json');
                $messages = json_decode($data, true);
                return count($messages);
            }

            //ha admin, akkor van gomb
            if (isAdminLoggedIn()) {
                echo '<li class="fejlec-nav-nyitos"><a href="php/uzenetek.php">' . bejegyzesSzaml() . ' db üzenet</a></li>';
            }
            ?>

            <li class="fejlec-nav-nyitos"><a href="index.php">Kezdőlap</a></li>
            <li class="fejlec-nav-nyitos"><a href="php/konyvek.php">Könyvek</a></li>
            <li class="fejlec-nav-nyitos"><a href="php/kapcsolat.php">Kapcsolat</a></li>
            <?php
            if(isset($_SESSION["user"])){
                echo ' <li class="fejlec-nav-nyitos"><a href="funkciok/logout.php">Kijelentkezés</a></li>
                     <li class="fejlec-nav-nyitos"><a href="php/profil.php">Profil</a></li>';
            }
            ?>
            <?php
            if(!isset($_SESSION["user"])){
                echo  '<li class="fejlec-nav-nyitos"><a href="php/login.php">Bejelentkezés</a></li>
                  <li class="fejlec-nav-nyitos"><a href="php/reg.php">Regisztráció</a></li>';
            }
            ?>
            <?php
            if(isset($_SESSION["user"])) { //ha be van jelntkezve
                // akkor ellenorizzuk hogy a koar letezik e sessionben
                if (!isset($_SESSION['kosar'])) {
                    $_SESSION['kosar'] = array(); // ha nem letrehozzuk
                }
                if (count($_SESSION['kosar']) < 1) { //ha tobb mint egy bejegyzes van benne, akkor a szammal irja ki a a kosarat
                    echo '<li class="fejlec-nav-kepes"><a href="php/kosar.php">
                    <img src="img/minecart.webp" height="30" alt="Kosár">Kosár</a></li>';
                } else { //ha nincs bajagyzes akkor szam nelkül
                    echo '<li class="fejlec-nav-kepes"><a href="php/kosar.php">
                    <img src="img/minecart.webp" height="30" alt="Kosár">Kosár (' . count($_SESSION['kosar']) . 'db termék)</a></li>';
                }
            } else { //ha nincs bejelentkezve, akkor is szam nelkül jeleniti meg
                echo '<li class="fejlec-nav-kepes"><a href="php/kosar.php">
                    <img src="img/minecart.webp" height="30" alt="Kosár">Kosár</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>

<!--oldal--------------------------------------------------------------------------->



<main>
    <div class="fullkepernyo">
        <table>
            <tr>
                <td>
                    <p class="kalandok">Oldalak között rejtőző kalandok</p><br>
                    <p class="konyvesbolt">Szegedi könyvesbolt</p>
                </td>
            </tr>
        </table>
    </div>


        <table id="why-table">
            <tr>
                <th colspan="3">Miért válassz minket?</th>
            </tr>
            <tr>
                <td><img src="img/konyvecske.webp" alt="könyv"><br>Többszáz eladott könyv</td>
                <td><img src="img/eredmeny.png" alt="érem"> <br>100%-os elégedettség</td>
                <td><img src="img/tapasztalat.png" alt="tapasztalat"><br>10 év tapasztlat</td>
            </tr>
        </table>


    <h1 class="cim">Megalakulásunk története</h1>

    <div class="container">
        <div id="slideshow">
            <div class="slide-wrapper">

                <div class="slide">
                    <h1 class="slide-number">
                        <img src="img/konyv2.jpg" alt="könyv" class="elso" width="728" height="510">

                    </h1>
                </div>
                <div class="slide">
                    <h1 class="slide-number">
                        <img src="img/konyv3.jpg" alt="könyv" class="masodik" width="728" height="510">
                    </h1>
                </div>
                <div class="slide">
                    <h1 class="slide-number">
                        <img src="img/maskonyv.jpg" alt="könyv" class="harmadik" width="728" height="510">
                    </h1>
                </div>
                <div class="slide">
                    <h1 class="slide-number">
                        <img src="img/konyv5.jpg" alt="könyv" class="harmadik" width="728" height="510">
                    </h1>
                </div>
            </div>
        </div>
        


        <div class="konyvek">
            <div>
                <p class="vonal">Két jó barát vagyunk, akik rajongunk az irodalomért, a könyvekért, és álmodoztunk arról, hogy egyszer egy saját könyvesboltot nyithassunk. Maga az ötlet egy napsütötte reggelen bontakozott ki, amikor éppen egy könyvtárban ábrándoztunk arról, hogy mi hogyan terveznénk meg a saját üzletünket.
                    Annyira belelkesedtünk az ötlettől, hogy azonnal nekiláttunk komolyabban tervezni. Napokig kutattunk az ideális helyszín után, és végül megtaláltuk a számunkra tökéletes helyet, mely a Kárász utca 7. alatt található. Ezután létre hoztuk webshopunkat, ahol könnyedén tudtok válogatni a megannyi könyvből.
                    Ehhez kéz a kézben jártunk végig antikváriumi vásárokat, keresve a tökéletes könyveket. Üzletünkben egyedi bútorokkal teremtettünk otthonos hangulatot.
                    Nálunk nem csak vásárolni tudsz könyveket, hanem programokon is részt tudsz venni. Így vált üzletünk hamarosan a helyi irodalmi élet központjává, ahol irodalmi esteket, könyvbemutatókat és olvasóköröket szervezünk napról-napra.</p>
            </div>
        </div>
    </div>

    <div class="kapcs">
        <h1>Szeretettel várunk téged is a könyvesboltunkba!</h1>
        <a href="php/kapcsolat.php" class="button">Kapcsolatfelvétel</a>

    </div>

</main>


</body>
</html>