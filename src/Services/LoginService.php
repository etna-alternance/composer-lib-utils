<?php

/**
 * PHP version 7.1
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Utils\Services;

/**
 * Classe permettant la génération de logins ETNA.
 */
class LoginService
{
    /**
     * Remplace tout les caractères accentués par leur versions sans accents.
     *
     * @param string $string La chaine de caractères sur la quelle effectuer ce remplacement
     *
     * @return string
     */
    public function removeAccents($string)
    {
        $accents      = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig|tilde|ring|orn|slash|th);/';
        $name_encoded = htmlentities($string, ENT_NOQUOTES, 'UTF-8');

        return preg_replace($accents, '$1', $name_encoded);
    }

    /**
     * Genere les login.
     *
     * @param string $lastname  Nom
     * @param string $firstname Prenom
     *
     * @return string
     */
    public function generate($lastname, $firstname)
    {
        if (empty(trim($lastname)) || empty(trim($firstname))) {
            throw new \Exception("Firstname and lastname can't be empty to generate login", 400);
        }

        $normalized_names = [
            strtolower(self::removeAccents($firstname)),
            strtolower(self::removeAccents($lastname)),
        ];

        $lastname  = str_replace(' ', '', $normalized_names[1]);
        $firstname = str_replace(' ', '', $normalized_names[0]);

        return substr($lastname, 0, 6) . '_' . $firstname[0];
    }
}
