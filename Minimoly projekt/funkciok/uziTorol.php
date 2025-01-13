<?php
session_start();

// megnezi, hogy a felh az admin e
function isAdminLoggedIn() {
    return isset($_SESSION['user']) && $_SESSION['user']['admin'] === true;
}

//ha admin es ki van toltve, akkor mehetunk tovabb
if (isAdminLoggedIn() && isset($_POST['tolosUziId'])) {
    $torlendo = $_POST['tolosUziId'];

    //kapcsolat.json fajlt betoltjuk
    $data = file_get_contents('../users/kapcs.json');
    $uzenetek = json_decode($data, true);

    //megnezzuk, hogy van e ilyen id es meghat az indexet
    $torlendoIndex = array_search($torlendo, array_column($uzenetek, 'id'));
                    //letrehoz egy tombot csak a letezo azonositokbol
                                                       //ez pedig megnezi, hogy hol van benne a torlendo index, ha letezik, visszaadja, egyebkent false

    // ha az id letezik, eltavolitja a tombbol
    if ($torlendoIndex !== false) {
        unset($uzenetek[$torlendoIndex]);
        //az adott indexen levo elemet torli a tombbol

        // ujraindexeljuk a tombot
        $uzenetek = array_values($uzenetek);

        // frissiti a kapcsolat.json fajlt
        file_put_contents('../users/kapcs.json', json_encode($uzenetek, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

//vissza a fooldalra, hogy frissuljenek az elemek
header("Location: ../php/uzenetek.php");
exit;
?>
