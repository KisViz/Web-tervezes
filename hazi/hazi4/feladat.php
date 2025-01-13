<?php

function belepo($nev)
{
    if (!is_string($nev)) {
        return null;
    } else {
        if ($nev === trim($nev)) {
            return true;
        } else {
            return false;
        }
    }
}
//var_dump(belepo('55 '));

function lakat($lakat, $zar)
{
    if ($lakat == $zar && $lakat !== $zar) {
        return true;
    } else {
        return false;
    }
}
//var_dump(lakat('TITKOS', 'TITKOS'));
//echo '<br>';
//var_dump(lakat(64, '64'));

function belepojegy($ar, $emberekSzama = 1)
{
    if (is_int($emberekSzama)) {
        return $ar * $emberekSzama;
    } else {
        return null;
    }
}
//var_dump(belepojegy(4000, 3)); // 12000
//echo '<br>';
//var_dump(belepojegy(5000, 2.5)); // null
//echo '<br>';
//var_dump(belepojegy(2000)); // 2000

function allatok($nevek)
{
    $ki = "Az allatkert allatai: " . implode(', ', $nevek) . ".";
    return $ki;
}
//echo allatok(['unikornisok', 'minotauruszok']);
//echo '<br>';
//echo allatok(['fonixek']);

function nyilvantartas($nevek, $allatokMennyisege, $allatTipusok)
{
    if (count($nevek) !== count($allatokMennyisege) || count($nevek) !== count($allatTipusok)) {
        return null;
    } else {
        $nyilvantartas = array();
        for ($i = 0; $i < count($nevek); $i++) {
            $nyilvantartas[] = array(
                'nev' => $nevek[$i],
                'darab' => $allatokMennyisege[$i],
                'tipus' => $allatTipusok[$i]
            );
        }

        return $nyilvantartas;
    }
}
//var_dump(nyilvantartas(['unikornisok', 'minotauruszok', 'ads'],[7, 11, 2],['novenyevo', 'ragadozo']));

function kobold_harc($nevek)
{
    if (count($nevek) % 2 == 1) {
        return null;
    }

    sort($nevek);
    $uj = array();
    for ($i = 0; $i < count($nevek) - 1; $i += 2) {
        $uj[] = $nevek[$i] . ' vs ' . $nevek[$i + 1];
    }
    return $uj;
}
//var_dump(kobold_harc(['Quirkit', 'Zizzle', 'Wisp', 'Bink']));

function fonixek($fajl)
{
    $sorok = file($fajl, FILE_IGNORE_NEW_LINES);
    $szaml = 0;

    foreach ($sorok as $sor) {
        $adott = explode(";", $sor);
        if ($adott[0] === 'HAMU' && $adott[1] >= 7) {
            $szaml++;
        }
    }

    return $szaml;
}
//echo fonixek("pelda.txt");

function vizihalak($fajl, $nevek)
{
    $f = fopen($fajl, "w");
    sort($nevek);

    for ($i = count($nevek) - 1; $i >= 0; $i--) {
        fwrite($f, $nevek[$i] . "\n");
    }

    fclose($f);
}
//vizihalak('output.txt', ['Aerolith', 'Magmathon', 'Electros', 'Cryonax']);