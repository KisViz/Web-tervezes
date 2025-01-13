<?php
session_start();

// megnezzuk van e kosar session
if (!isset($_SESSION['kosar'])) {
    $_SESSION['kosar'] = array(); // ha ninc létrehozzuk
}

//ha ki van toltve, akkor mehetunk tovabb
if (isset($_POST['torlendoTemekId'])) {
    $termekId = $_POST['torlendoTemekId'];

    //valtozonak atatdjuk a kosar tartalmat
    $termekek = $_SESSION['kosar'];


    //megnezzuk, hogy van e ilyen id es meghat az indexet
    $torlendoIndex = array_search($termekId, array_column($termekek, 'id'));
                    //letrehoz egy tombot csak a letezo azonositokbol
                                             //ez pedig megnezi, hogy hol van benne a torlendo index, ha letezik, visszaadja, egyebkent false

    // ha az id letezik, eltavolitja a tombbol
    if ($torlendoIndex !== false) {
        unset($termekek[$torlendoIndex]);
        //az adott indexen levo elemet torli a tombbol

        // ujraindexeljuk a tombot
        $termekek = array_values($termekek);

        // vissza tesszuk a frissitett tombo a kosarba
        $_SESSION['kosar'] = $termekek;
    }
}

//vissza a fooldalra, hogy frissuljenek az elemek
header("Location: ../php/kosar.php");
exit;
?>
