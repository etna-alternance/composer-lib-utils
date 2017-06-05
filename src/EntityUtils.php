<?php

namespace ETNA\Utils;

class EntityUtils
{
    /**
     * Effectue la comparaison entre 2 tableaux et renvoie les différences
     *
     * @param  array $old
     * @param  array $new
     * @param  array $translations Traductions des champs
     * @param  array $exclusions   Champs à ne pas prendre en compte
     *
     * @return array
     */
    public static function getChanges(array $old, array $new, array $translations = [], array $exclusions = [])
    {
        $changes = [];

        // On récupère toutes les clés différentes et on en filtre quelques unes
        $fields = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($fields as $field) {
            if (in_array($field, $exclusions)) {
                continue;
            }

            // Si nous avons une traduction du champ, alors on l'utilisera sinon, nous avons une string par défaut
            $field_translation = "du {$field}";
            if (true === isset($translations[$field])) {
                $field_translation = $translations[$field];
            }

            switch (true) {
                case (self::checkEmptyField($old, $new, $field)):
                    $changes[] = "* Ajout {$field_translation} '{$new[$field]}'";
                    break;
                case (self::checkEmptyField($new, $old, $field)):
                    $changes[] = "* Suppression {$field_translation} '{$old[$field]}'";
                    break;
                case (true === isset($old[$field]) && true === isset($new[$field]) && $old[$field] !== $new[$field]):
                    $changes[] = "* Changement {$field_translation} '{$old[$field]}' => '{$new[$field]}'";
                    break;
                default:
                    break;
            }
        }

        return $changes;
    }

    private static function checkEmptyField(array $a_array, array $b_array, $field)
    {
        return (
            false === isset($a_array[$field]) &&
            true === isset($b_array[$field]) &&
            '' !== trim($b_array[$field])
        ) || (
            true === isset($a_array[$field]) &&
            '' === trim($a_array[$field]) &&
            true === isset($b_array[$field]) &&
            '' !== trim($b_array[$field])
        );
    }
}
