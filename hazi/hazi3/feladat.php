<?php

function hozzavalok_szama($nevek)
{
/*    $i = 0;
    foreach ($nevek as $n) {
        $i++;
    }
    return $i;*/
    return count($nevek);
}
//$aha = ['Szikragyoker', 'Suttogo moha', 'Holdvirag'];
//echo hozzavalok_szama($aha);

function legnagyobb_mennyiseg($nevek)
{
    if (empty($nevek)) {
        return NULL;
    } else {
        $maxmennyiseg = -1;
        $maxnev = '';
        foreach ($nevek as $nev => $mennyiseg) {
            if ($mennyiseg > $maxmennyiseg) {
                $maxmennyiseg = $mennyiseg;
                $maxnev = $nev;
            }
        }
        return $maxnev;
    }
}

//$asdf = ['Szikragyoker' => 3, 'Suttogo moha' => 7, 'Holdvirag' => 4];
//echo legnagyobb_mennyiseg($asdf);

function hozzavalok_beszerzese($kellenek, $talalt)
{
    return in_array($talalt, $kellenek);
}

//$asdfg = ['Szikragyoker', 'Suttogo moha', 'Holdvirag'];
//var_dump(hozzavalok_beszerzese($asdfg,  'Fura gomba'));

function rendszerezes($szoveg)
{
    $darabolt = explode(';',$szoveg);
    $ujtomb = [];

    foreach ($darabolt as $darab) {
        $szaml = 0;
        foreach ($darabolt as $szamol) {
            if ($darab == $szamol) {
                $szaml++;
            }
        }
        if (!in_array($darab, $ujtomb)) {
            $ujtomb[$darab] = $szaml;
        }
    }

    return $ujtomb;
}

//var_dump(rendszerezes('Szikragyoker;Suttogo moha;Szikragyoker;Holdvirag;Szikragyoker'));

function varazslat_elokeszitese($ige)
{
    $ige = strtoupper($ige);
    for ($i = 0; $i < strlen($ige) - 1; $i++) {
        if ($ige[$i] == $ige[$i + 1]) {
            return true;
        }
    }
    return false;
}

//var_dump( varazslat_elokeszitese('ABKDOKBLXK'));
//var_dump( varazslat_elokeszitese('BKkIODBKA'));

function fozes(&$ust, $osszetevo)
{
    $ust[] = $osszetevo;

//    return $ust;
}

//$asd = ['Szikragyoker', 'Suttogo moha'];
//echo fozes($asd, 'Holdvirag');