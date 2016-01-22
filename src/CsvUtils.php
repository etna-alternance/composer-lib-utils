<?php

namespace ETNA\Utils;

class CsvUtils
{
    /**
     * Recupère un flat tableau depuis un tableau associatif multi niveau
     *
     * @param  array  $array  un etudiant sous forme d'array associatif multi-niveau
     * @param  string $prefix le prefix a utilisé pour la clé associative
     *
     * @return array flat array
     */
    public static function getTokenFromArray($array, $prefix = '')
    {
        $tokens = [];
        foreach ($array as $key => $value) {
            $sub_prefix = (false === empty($prefix)) ? "{$prefix}_{$key}" : $key;
            if (is_array($value)) {
                $tokens = array_merge($tokens, self::getTokenFromArray($value, $sub_prefix));
            } else {
                $tokens[$sub_prefix] = utf8_decode($value);
            }
        }
        return $tokens;
    }

    /**
     * Prends un tableau PHP et en génère un csv
     *
     * @param  array    $array     Array a transformer
     * @param  int|null &$csv_rows Nombre de rows générées
     *
     * @return string
     */
    public static function arrayToCsv(array $array, int &$csv_rows = null)
    {
        if (true === empty($array)) {
            $csv_rows = 0;
            return "";
        }

        $headers = array_keys($array[0]);
        $tokens  = array_values($array);

        $csv = self::sputcsv($headers, ';', '"', "\n");
        foreach ($tokens as $value) {
            if (!empty(array_diff(array_keys($value), $headers))) {
                throw new \Exception("Bad csv", 400);
            }

            $cleanArray = str_replace("\n", " ", array_values($value));
            $csv .= self::sputcsv($cleanArray, ';', '"', "\n");
        }
        $csv      = substr_replace($csv, "", -1);
        $csv_rows = count($tokens);

        return $csv;
    }

    /**
     * Vu que sputcsv n'existe pas dans php :'(
     * fonction qui retourne une string csv a partir de l'array fourni
     *
     * @param array  $row       Le tableau contenant les données à csvifier
     * @param string $delimiter Le caractère délimitant les champs csv
     * @param string $enclosure Le caractère à utiliser pour echapper
     * @param string $eol       Le caractère d'EndOfFile
     *
     * @return false|string
     */
    public static function sputcsv(array $row, $delimiter = ',', $enclosure = '"', $eol = "\n")
    {
        static $file_pointer = false;

        if (false === $file_pointer) {
            $file_pointer = fopen('php://temp', 'r+');
        } else {
            rewind($file_pointer);
        }

        if (false === fputcsv($file_pointer, $row, $delimiter, $enclosure)) {
            return false;
        }

        rewind($file_pointer);
        $csv = fgets($file_pointer);

        if (PHP_EOL !== $eol) {
            $csv = substr($csv, 0, (0 - strlen(PHP_EOL))) . $eol;
        }

        return $csv;
    }
}
