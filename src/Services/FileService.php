<?php

/**
 * PHP version 7.1
 * @author BLU <dev@etna-alternance.net>
 */

declare(strict_types=1);

namespace ETNA\Utils\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Classe permettant la récupération d'informations dans des fichiers.
 */
class FileService
{
    /**
     * Récupere le contenu d'un fichier JSON ou CSV et le transforme en array PHP.
     *
     * @param UploadedFile $file Le fichier à parser
     *
     * @return object|int|float|array|bool|null
     */
    public function handleFile(UploadedFile $file)
    {
        $content   = null;
        $extension = $file->guessExtension();

        if ('txt' === $extension) {
            $file    = file_get_contents($file->getPathname());
            $content = json_decode($file, true);

            if (\is_string($content) || null === $content) {
                $content = self::handleCsvFile($file);
            }
        }

        return $content;
    }

    /**
     * Transforme une string CSV en array PHP.
     *
     * @param string $content
     *
     * @return array
     */
    private function handleCsvFile($content)
    {
        $content = utf8_encode($content);
        $content = array_filter(
            explode("\n", $content),
            function ($row) {
                return !empty($row);
            }
        );
        $content = array_map(
            function ($row) {
                return str_getcsv($row, ';', '"', "\n");
            },
            $content
        );
        $header = array_shift($content);
        foreach ($content as $key => $row) {
            if (\count($header) !== \count($row)) {
                throw new \Exception('Bad CSV given', 400);
            }
            $content[$key] = array_combine($header, $row);
        }

        return $content;
    }
}
