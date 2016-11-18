<?php

namespace TestContext;

use ETNA\FeatureContext\BaseContext;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use ETNA\FeatureContext as EtnaFeatureContext;
use ETNA\Utils\CsvUtils;
use ETNA\Utils\FileUtils as EtnaFileUtils;
use ETNA\Utils\LoginUtils;
use ETNA\Utils\NotifyUtils;
use ETNA\Utils\PasswordUtils;

/**
 * Features context
 */
class FeatureContext extends BaseContext
{
    static private $_parameters;
    static private $vhosts;

    private $result    = null;
    private $csv_lines = null;
    private $error     = null;

    /**
     * @BeforeScenario @Sprinter
     */
    public static function createSprinterQueues()
    {
        $channel = self::$silex_app['rabbit.producer']['sprinter']->getChannel();
        $channel->exchange_declare('SPrinter', 'direct', false, true, false);

        foreach (['lefran_f', 'norris_c'] as $sprinter_user) {
            $queue_opt = self::$silex_app['rmq.queues']["sprinter.{$sprinter_user}"];
            $channel->queue_declare(
                $queue_opt["name"],
                $queue_opt["passive"],
                $queue_opt["durable"],
                $queue_opt["exclusive"],
                $queue_opt["auto_delete"]
            );

            $channel->queue_bind($queue_opt['name'], $queue_opt['exchange'], $queue_opt['routing.key']);
        }
    }

    /**
     * @When /^je convertis en csv le tableau contenu dans "([^"]*)"(?: en prefixant avec "([^"]*)"?)?$/
     */
    public function jeConvertisEnCsvLeTableauContenuDans($filename, $prefix = null)
    {
        $filepath = realpath($this->requests_path . "/" . $filename);

        $array_to_convert = file_get_contents($filepath);
        $array_to_convert = json_decode($array_to_convert, true);

        if (null === $array_to_convert) {
            throw new Exception("json_decode error");
        }

        $array_to_convert = array_map(
            function ($line) use ($prefix) {
                return CsvUtils::getTokenFromArray($line, $prefix);
            },
            $array_to_convert
        );

        try {
            $this->result = CsvUtils::arrayToCsv($array_to_convert, $this->csv_lines);
        } catch (\Exception $exception) {
            $this->error = $exception;
        }

    }

    /**
     * @When /^j'envoie un mail a "([^"]*)" avec "([^"]*)" avec le titre "([^"]*)" et le template contenu dans le fichier "([^"]*)" et les tokens contenus dans "([^"]*)"(?: avec comme pièce jointe les fichiers "([^"]*)"?)?(?: avec en copie les emails "([^"]*)"?)?$/
     */
    public function jEnvoieUnMail($to_email, $from_email, $title, $template_filename, $tokens_filename, $files = null, $cc = null)
    {
        $template_filepath = realpath($this->requests_path . "/" . $template_filename);
        $template          = file_get_contents($template_filepath);
        $tokens_filepath   = realpath($this->requests_path . "/" . $tokens_filename);
        $tokens_content    = file_get_contents($tokens_filepath);
        $tokens            = json_decode($tokens_content, true);

        $mail_opt = [];
        if (null !== $files && false === empty($files)) {
            $mail_opt = [
                "files" => NotifyUtils::prepareFilesForMail(
                    array_map(
                        function ($filename) {
                            $filename = trim($filename);
                            return [
                                "name" => $filename,
                                "path" => "{$this->requests_path}/{$filename}"
                            ];
                        },
                        explode(";", $files)
                    )
                )
            ];
        }

        if (null !== $cc) {
            $cc       = [
                "cc" => explode(";", $cc)
            ];
            $mail_opt = true === empty($mail_opt) ? $cc : array_merge($cc, $mail_opt);
        }

        try {
            NotifyUtils::sendMail(self::$silex_app, $title, $template, $from_email, $to_email, $tokens, $mail_opt);
        } catch (\Exception $exception) {
            $this->error = $exception;
        }
    }

    /**
     * @When /^je lance une impression avec le template "([^"]*)" et les données contenues dans "([^"]*)"(?: dans la queue "([^"]*)"?)?$/
     */
    public function jeLanceUneImpression($template_filename, $tokens_filename, $queue_name = null)
    {
        $template_filepath = realpath($this->requests_path . "/" . $template_filename);
        $template          = file_get_contents($template_filepath);
        $tokens_filepath   = realpath($this->requests_path . "/" . $tokens_filename);
        $tokens_content    = file_get_contents($tokens_filepath);
        $tokens            = json_decode($tokens_content, true);

        try {
            NotifyUtils::sendPrint(self::$silex_app, $template_filename, $template, $queue_name, $tokens);
        } catch (\Exception $exception) {
            $this->error = $exception;
        }
    }

    /**
     * @Then /^le résultat devrait être identique à "(.*)"$/
     * @Then /^le résultat devrait être identique au JSON suivant :$/
     * @Then /^le résultat devrait ressembler au JSON suivant :$/
     * @param string $string
     */
    public function leResultatDevraitRessemblerAuJsonSuivant($string)
    {
        $expected_result = json_decode($string);
        $real_result     = json_decode(json_encode($this->result));
        if (null === $expected_result) {
            throw new Exception("json_decode error");
        }

        $this->check($expected_result, $real_result, "result", $errors);
        if (0 < ($nb_errors = count($errors))) {
            echo json_encode($real_result, JSON_PRETTY_PRINT);
            throw new Exception("{$nb_errors} errors :\n" . implode("\n", $errors));
        }
    }

    /**
     * @Then /^le résultat devrait être identique au fichier "(.*)"$/
     */
    public function leResultatDevraitRessemblerAuFichier($file)
    {
        $file = realpath($this->results_path . "/" . $file);
        $this->leResultatDevraitRessemblerAuJsonSuivant(file_get_contents($file));
    }

    /**
     * @Then /^le résultat devrait être identique au fichier csv "(.*)"$/
     */
    public function leResultatDevraitRessemblerAuFichierCsv($filename)
    {
        $filepath         = realpath($this->results_path . "/" . $filename);
        $expected_content = trim(file_get_contents($filepath), "\n");

        if ($expected_content !== $this->result) {
            echo "\n", $expected_content, "\n\n";
            echo $this->result, "\n\n";
            throw new Exception("CSVs results are not the same");
        }
    }

    /**
     * @Then /^le csv sortant devrait contenir (\d+) lignes$/
     */
    public function leCsvSortantDevraitContenirTantDeLignes($expected_nb_lines)
    {
        if ($this->csv_lines !== intval($expected_nb_lines)) {
            throw new Exception("Expected {$expected_nb_lines} CSV lines but got {$this->csv_lines}");
        }
    }

    /**
     * @When /^je génère un mot de passe(?: avec (\d+) lettres(?: et (\d+) autres caractères)?)?$/
     */
    public function jeGenereUnMotDePasse($letters = 6, $non_letters = 2)
    {
        $this->result = PasswordUtils::generate($letters, $non_letters);
    }

    /**
     * @Then /^le mot de passe devrait contenir (\d+) lettres et (\d+) autres caractères$/
     */
    public function leMotDePasseDevraitContenir($letters, $non_letters)
    {
        $regex  = "/(?=^([^A-Za-z]*[a-zA-Z]){" . $letters;
        $regex .= "}[^A-Za-z]*$)(?=^([^0-9\.]*[0-9\.]){" . $non_letters . "}[^0-9\.]*$)/";

        if (1 !== preg_match($regex, $this->result)) {
            throw new Exception("Generated password does not match");
        }
    }

    /**
     * @When /^je chiffre le mot de passe "([^"]*)"$/
     */
    public function jeChiffreLeMotDePasse($password)
    {
        $this->result = PasswordUtils::encrypt($password);
    }

    /**
     * @When /^je veux générer un login avec le nom "([^"]*)" et le prénom "([^"]*)"$/
     */
    public function jeVeuxGenererUnLogin($lastname, $firstname)
    {
        try {
            $this->result = LoginUtils::generate($lastname, $firstname);
        } catch (\Exception $exception) {
            $this->error = $exception;
        }
    }

    /**
     * @When /^je veux récupérer le contenu du fichier "([^"]*)"$/
     */
    public function jeVeuxRecupererLeContenuDuFichier($filename)
    {
        $filepath = realpath($this->requests_path . "/" . $filename);
        $file     = new UploadedFile($filepath, $filename);

        try {
            $this->result = EtnaFileUtils::handleFile($file);
        } catch (\Exception $exception) {
            $this->error = $exception;
        }
    }

    /**
     * @Then /^il devrait y avoir eu une erreur$/
     */
    public function ilDevraitYavoirEuUneErreur()
    {
        if (null === $this->error) {
            throw new \Exception("Expecting an error to happen but everything went good");
        }
    }

    /**
     * @Then /^il ne devrait pas y avoir eu une erreur$/
     */
    public function ilNeDevraitPasYavoirEuUneErreur()
    {
        if (null !== $this->error) {
            throw new \Exception("Wasn't expecting an error to happen but went bad : {$this->error->getMessage()}");
        }
    }

    /**
     * @Then /^le message d'erreur devrait être "([^"]*)"(?: et le code (\d+)?)$/
     */
    public function leMessageDerreurDevraitEtre($msg, $code = null)
    {
        $code = null === $code ?: intval($code);
        if (null === $this->error || $this->error->getMessage() !== $msg) {
            throw new Exception("Expecting error message to be \"{$msg}\" but got \"{$this->error->getMessage()}\"");
        }
        if (null !== $code && $this->error->getCode() !== $code) {
            throw new Exception("Expecting error code to be \"{$code}\" but got \"{$this->error->getCode()}\"");
        }
    }
}
