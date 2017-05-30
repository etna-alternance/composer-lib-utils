<?php

namespace ETNA\Utils;

class EntityUtils
{
    /**
     * Effectue la comparaison entre 2 tableaux et renvoie les différences
     *
     * @param  array $old
     * @param  array $new
     *
     * @return array
     */
    public static function getDiff($old, $new, $translations = [], $exclusions = [])
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
                case (false === isset($old[$field]) && true === isset($new[$field]) && '' !== trim($new[$field])):
                case (true === isset($old[$field]) &&
                    '' === trim($old[$field]) &&
                    true === isset($new[$field]) &&
                    '' !== trim($new[$field])
                ):
                    $changes[] = "* Ajout {$field_translation} '{$new[$field]}'";
                    break;
                case (true === isset($old[$field]) && '' !== trim($old[$field]) && false === isset($new[$field])):
                case (true === isset($old[$field]) &&
                    '' !== trim($old[$field]) &&
                    true === isset($new[$field]) &&
                    '' === trim($new[$field])
                ):
                    $changes[] = "* Suppression {$field_translation} '{$old[$field]}'";
                    break;
                case (true === isset($old[$field]) && true === isset($new[$field]) && $old[$field] !== $new[$field]):
                    $changes[] = "* Changement {$field_translation} '{$old[$field]}'' => '{$new[$field]}'";
                    break;
                default:
                    break;
            }
        }

        return $changes;
    }
}
