<?php

namespace ETNA\Utils;

use Silex\Application;

class NotifyUtils
{
    /**
     * Permet d'imprimer un tableau associatif $sprinter_data
     * transforme le tableau en csv et l'envoi au sprinter d'ecrit dans $routing_key
     *
     * @param Application $app             Silex application
     * @param string      $letter_title    Le nom du document .docx à générer
     * @param string      $letter_template Le template .docx à générer
     * @param string      $routing_key     Le nom de la routing key
     * @param array       $sprinter_data   Le tableaux associatif contenant les informations à imprimer
     * @param array       $sprinter_opt    Le tableaux d'options optionnelles pour sprinter
     *
     * @return string routing_key
     */
    public static function sendPrint(
        Application $app,
        $letter_title,
        $letter_template,
        $routing_key,
        array $sprinter_data,
        array $sprinter_opt = []
    ) {
        if (!is_array($sprinter_data) || empty($sprinter_data)) {
            throw new \Exception("Bad data provided for printing", 400);
        }
        $letter_template_b64 = base64_encode($letter_template);
        $csv                 = CsvUtils::arrayToCsv($sprinter_data, $sprinter_opt["csv_rows"]);
        $csv_base64          = base64_encode($csv);

        $template = [
            "Name"    => $letter_title,
            "Content" => $letter_template_b64,
        ];

        $data = [
            "Name"    => "data.csv",
            "Content" => $csv_base64,
        ];

        $routing_key = (null === $routing_key) ?
            $app["sprinter.options"]["default.routing_key"] : $routing_key;

        $app["sprinter"]->sendPrint($template, $data, true, $routing_key, $sprinter_opt);

        return $routing_key;
    }

    /**
     * Envoi un mail via rabbitMq
     *
     * @param Application $app            Silex application
     * @param string      $email_title    Le titre de l'email
     * @param string      $email_template Le template du mail
     * @param string      $email_to       L'email du destinataire
     * @param array       $mail_data      Le tableau de tokens qui seront remplacés dans le template
     *
     * @return string L'adresse mail a laquelle le mail a été envoyé
     */
    public static function sendMail(
        Application $app,
        $email_title,
        $email_template,
        $email_from,
        $email_to,
        array $mail_data,
        array $email_opt = []
    ) {
        if (!isset($email_to) || trim($email_to) === "" || !isset($email_from) || trim($email_from) === "") {
            throw new \Exception("No email provided", 400);
        }

        $tokens = array_map(
            function($token) {
                return "{{$token}}";
            },
            array_keys($mail_data)
        );

        $template = str_replace($tokens, array_values($mail_data), $email_template);
        $mail     = [
            "from"    => $email_from,
            "to"      => $email_to,
            "subject" => $email_title,
            "content" => $template
        ];

        $mail = array_merge($mail, $email_opt);

        $app["amqp.queues"]["email"]->send($mail);

        return $email_to;
    }
}
