<?php

namespace ETNA\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUtils
{
    /**
     * @param UploadedFile $file
     *
     * @return object|integer|double|array|boolean|null
     */
    public static function handleFile(UploadedFile $file)
    {
        $content   = null;
        $extension = $file->guessExtension();

        if ("txt" === $extension) {
            $file    = file_get_contents($file);
            $content = json_decode($file, true);

            if (is_string($content) || null === $content) {
                $content = self::handleCsvFile($file);
            }
        }

        return $content;
    }

    /**
     * @param string $content
     *
     * @return array
     */
    private static function handleCsvFile($content)
    {
        $content = utf8_encode($content);
        $content = array_filter(
            explode("\n", $content),
            function($row) {
                return !empty($row);
            }
        );
        $content = array_map(
            function($row) {
                return str_getcsv($row, ";", '"', "\n");
            },
            $content
        );
        $header = array_shift($content);
        foreach ($content as $key => $row) {
            if (count($header) !== count($row)) {
                throw new \Exception("Bad CSV given", 400);
            }
            $content[$key] = array_combine($header, $row);
        }

        return $content;
    }
}
