<?php

/**
 * This script was adapted from the example made by @newerton found at 
 * https://newerton.com.br/blog/12-preco-em-real-por-extenso-em-php.html
 */

class NumberToText
{

    public static function convertNumberText($value, $uppercase = 0): string
    {
        $value = self::convertFormat($value);
        $singular = ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
        $plural = ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];
        $hundreds = ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];
        $dozens = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
        $dozens10 = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];
        $unit = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];

        $group_of_numbers = explode(".", $value);
        $number_of_groups = count($group_of_numbers);
        $group_of_numbers = self::completeWithZeros(
            $number_of_groups, 
            $group_of_numbers
        );
        $final_result = self::endResultGenerator(
            $value,
            $singular,
            $plural,
            $hundreds,
            $dozens,
            $dozens10,
            $unit,
            $group_of_numbers,
            $number_of_groups
        );

        if (!$uppercase || $uppercase == 0 || $uppercase == "") {
            return trim($final_result ? $final_result : "zero");
        }

        if ($uppercase >= 1) {
            return trim(mb_strtoupper($final_result) ? mb_strtoupper(mb_strtoupper($final_result)) : "Zero");
        }
    }

    private function convertFormat($value): string
    {
        if (strpos($value, ",") > 0) {
            $value = str_replace(".", "", $value);
            $value = str_replace(",", ".", $value);
        }
        $value = number_format($value, 2, ".", ".");

        return $value;
    }

    private function completeWithZeros($number_of_groups, $group_of_numbers): array
    {

        for ($i = 0; $i < $number_of_groups; $i++) {
            for ($ii = strlen($group_of_numbers[$i]); $ii < 3; $ii++) {
                $group_of_numbers[$i] = "0" . $group_of_numbers[$i];
            }
        }

        return $group_of_numbers;

    }

    private function endResultGenerator(
        $value,
        $singular,
        $plural,
        $hundreds,
        $dozens,
        $dozens10,
        $unit,
        $group_of_numbers,
        $number_of_groups
    ): string 
    {
        $z = 0;
        $end = $number_of_groups - ($group_of_numbers[$number_of_groups - 1] > 0 ? 1 : 2);
        $final_result = '';

        for ($i = 0; $i < $number_of_groups; $i++) {

            $value = $group_of_numbers[$i];
            $rc = (($value > 100) && ($value < 200)) ? "cento" : $hundreds[$value[0]];
            $rd = ($value[1] < 2) ? "" : $dozens[$value[1]];
            $ru = ($value > 0) ? (($value[1] == 1) ? $dozens10[$value[2]] : $unit[$value[2]]) : "";
            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = $number_of_groups - 1 - $i;
            $r .= $r ? " " . ($value > 1 ? $plural[$t] : $singular[$t]) : "";

            if ($value == "000") {
                $z++;
            }
            if ($z > 0) {
                $z--;
            }
            if (($t == 1) && ($z > 0) && ($group_of_numbers[0] > 0)) {
                $r .= (($z > 1) ? " de " : "") . $plural[$t];
            }
            if ($r) {
                $final_result = $final_result . ((($i > 0) && ($i <= $end) &&
                    ($group_of_numbers[0] > 0) && ($z < 1)) ? (($i < $end) ? ", " : " e ") : " ") . $r;
            }

        }

        return $final_result;

    }

}

echo NumberToText::convertNumberText("2343,01", 1);
// result: DOIS MIL, TREZENTOS E QUARENTA E TRÊS REAIS E UM CENTAVO
