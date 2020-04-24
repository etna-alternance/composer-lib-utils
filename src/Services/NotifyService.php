<?php

/**
 * PHP version 7.1
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Utils\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service permettant l'envoi d'impressions ou de mails.
 */
class NotifyService
{
    /** @var ContainerInterface Le container de l'appli */
    private $container;

    /** @var CsvService Le service CSV */
    private $csv;

    /**
     * Constructeur de la classe.
     *
     * @param ContainerInterface $container Le container de l'appli
     * @param CsvService         $csv       Le service CSV
     */
    public function __construct(ContainerInterface $container, CsvService $csv)
    {
        $this->csv       = $csv;
        $this->container = $container;
    }

    /**
     * Permet d'imprimer un tableau associatif $sprinter_data
     * transforme le tableau en csv et l'envoi au sprinter d'ecrit dans $routing_key.
     *
     * @param string      $letter_title    Le nom du document .docx à générer
     * @param string      $letter_template Le template .docx à générer
     * @param string|null $routing_key     Le nom de la routing key
     * @param array       $sprinter_data   Le tableaux associatif contenant les informations à imprimer
     * @param array       $sprinter_opt    Le tableaux d'options optionnelles pour sprinter
     *                                     - printflag     : impression sur papier véritable
     *                                     - skip_generate : n'envoie pas le document dans les dossiers électroniques
     *                                     - send_mail     : envoi du document par mail après génération
     *                                     - mail_to       : destinataire du mail
     *                                     - mail_title    : titre du mail
     *                                     - mail_body     : contenu du mail
     *
     * @return string routing_key
     */
    public function sendPrint(
        string $letter_title,
        string $letter_template,
        ?string $routing_key,
        array $sprinter_data,
        array $sprinter_opt = []
    ): string {
        if (!$this->container->has('sprinter.sprinter_service')) {
            throw new \Exception('No sprinter service found', 500);
        }

        if (!\is_array($sprinter_data) || empty($sprinter_data)) {
            throw new \Exception('Bad data provided for printing', 400);
        }

        /** @var \ETNA\Sprinter\Services\SprinterService */
        $sprinter    = $this->container->get('sprinter.sprinter_service');
        $csv         = $this->csv->arrayToCsv($sprinter_data, $sprinter_opt['csv_rows']);
        $routing_key = (null === $routing_key) ? $sprinter->getDefaultRoutingKey() : $routing_key;

        $sprinter->sendPrint($letter_title, $letter_template, true, $routing_key, $csv, $sprinter_opt);

        return $routing_key;
    }

    /**
     * Prépare des fichiers pour de l'envoi par mail.
     *
     * @param array $files Fichiers sous la forme ["name" => "...", "path" => "..."]
     *
     * @return array Fichiers prêts pour pigeon voyageur
     */
    public function prepareFilesForMail(array $files): array
    {
        $prepared = [];

        foreach ($files as $file) {
            if (!isset($file['path']) || !isset($file['name']) || false === file_exists($file['path'])) {
                continue;
            }
            $prepared[] = [
                'name'    => $file['name'],
                'content' => base64_encode(file_get_contents($file['path'])),
            ];
        }

        return $prepared;
    }

    /**
     * Envoie un mail via rabbitMq.
     *
     * @param string $email_title    Le titre de l'email
     * @param string $email_template Le template du mail
     * @param string $email_from     L'email de l'expéditeur
     * @param string $email_to       L'email du destinataire (plusieurs séparés par une virgule)
     * @param array  $mail_data      Le tableau de tokens qui seront remplacés dans le template
     * @param array  $email_opt      Les options supplémentaires du mail
     *                               - cc  : destinataires par copie
     *                               - bcc : destinataires par copie cachée
     *
     * @return string L'adresse mail a laquelle le mail a été envoyé
     */
    public function sendMail(
        string $email_title,
        string $email_template,
        string $email_from,
        string $email_to,
        array $mail_data,
        array $email_opt = []
    ): string {
        if (!$this->container->has('old_sound_rabbit_mq.email_producer')) {
            throw new \Exception('No email rabbitmq producer found', 500);
        }
        if (!isset($email_to) || '' === trim($email_to) || !isset($email_from) || '' === trim($email_from)) {
            throw new \Exception('No email provided', 400);
        }

        $tokens = array_map(
            function ($token) {
                return "{{$token}}";
            },
            array_keys($mail_data)
        );

        $template = str_replace($tokens, array_values($mail_data), $email_template);
        $mail     = array_merge([
            'from'    => $email_from,
            'to'      => $email_to,
            'subject' => $email_title,
            'content' => $template,
        ], $email_opt);

        $this->container->get('old_sound_rabbit_mq.email_producer')->publish(json_encode($mail), 'email');

        return $email_to;
    }
}
