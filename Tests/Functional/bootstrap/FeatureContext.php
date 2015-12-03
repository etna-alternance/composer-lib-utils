<?php

use Behat\Behat\Context\BehatContext;

use Symfony\Component\HttpFoundation\File\UploadedFile;

putenv("APPLICATION_ENV=" . (false !== getenv("APPLICATION_ENV") ?: "testing"));

use ETNA\FeatureContext as EtnaFeatureContext;
use ETNA\Utils\CsvUtils;
use ETNA\Utils\FileUtils;
use ETNA\Utils\NotifyUtils;

/**
 * Features context
 */
class FeatureContext extends BehatContext
{
    use EtnaFeatureContext\Coverage;
    use EtnaFeatureContext\Check;
    use EtnaFeatureContext\setUpScenarioDirectories;
    use EtnaFeatureContext\SilexApplication;
    use EtnaFeatureContext\RabbitMQ;

    static private $_parameters;
    static $vhosts = ["/test-behat"];

    private $result    = null;
    private $csv_lines = null;
    private $error     = null;

    /**
     * Initialize context
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        self::$_parameters = $parameters;

        $result = null;
        ini_set('display_errors', true);
        ini_set('xdebug.var_display_max_depth', 100);
        ini_set('xdebug.var_display_max_children', 100);
        ini_set('xdebug.var_display_max_data', 100);
        error_reporting(E_ALL);
    }

    /**
     * @When /^je convertis en csv le tableau contenu dans "([^"]*)"(?: en prefixant avec "([^"]*)"?)?$/
     */
    public function jeConvertisEnCsvLeTableauMultidimensionnelContenuDans($filename, $prefix = null)
    {
        $filepath = realpath($this->requests_path . "/" . $filename);

        $array_to_convert = file_get_contents($filepath);
        $array_to_convert = json_decode($array_to_convert, true);

        if ($array_to_convert === null) {
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
     * @When /^j'envoie un mail a "([^"]*)" avec "([^"]*)" avec le titre "([^"]*)" et le template contenu dans le fichier "([^"]*)" et les tokens contenus dans "([^"]*)"$/
     */
    public function jEnvoieUnMail($to, $from, $title, $template_filename, $tokens_filename)
    {
        $template_filepath = realpath($this->requests_path . "/" . $template_filename);
        $template          = file_get_contents($template_filepath);
        $tokens_filepath   = realpath($this->requests_path . "/" . $tokens_filename);
        $tokens_content    = file_get_contents($tokens_filepath);
        $tokens            = json_decode($tokens_content, true);

        try {
            NotifyUtils::sendMail(self::$silex_app, $title, $template, $from, $to, $tokens);
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
     * @Then /^il doit y avoir un message dans la file "([^"]*)" avec le corps contenu dans "([^"]*)"$/
     */
    public function ilDoitYAvoirUnMessageDansLaFileAvecLeCorpsContenuDans($queue = null, $body = null)
    {
        if ($body !== null) {
            if (!file_exists($this->results_path . $body)) {
               throw new Exception("File not found : {$this->results_path}${body}");
            }
        }

        $body          = file_get_contents($this->results_path . $body);
        $parsed_wanted = json_decode($body);

        $channel = self::$silex_app["amqp.queues"][$queue]->getChannel();

        $response_msg    = $channel->basic_get($queue);
        $parsed_response = json_decode($response_msg->body);
        $this->check($parsed_wanted, $parsed_response, "result", $errors);
        if ($n = count($errors)) {
            echo json_encode($parsed_response, JSON_PRETTY_PRINT);
            throw new Exception("{$n} errors :\n" . implode("\n", $errors));
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
        if ($expected_result === null) {
            throw new Exception("json_decode error");
        }
        // print_r($expected_result); print_r($this->result);

        $this->check($expected_result, $real_result, "result", $errors);
        if (($nb_errors = count($errors)) > 0) {
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
        $filepath = realpath($this->results_path . "/" . $filename);
        $expected_content = trim(file_get_contents($filepath), "\n");

        if ($expected_content !== $this->result) {
            echo "\n", $expected_content,"\n\n";
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
     * @When /^je veux récupérer le contenu du fichier "([^"]*)"$/
     */
    public function jeVeuxRecupererLeContenuDuFichier($filename, $mime_type = null)
    {
        $filepath = realpath($this->requests_path . "/" . $filename);
        $file     = new UploadedFile($filepath, $filename);

        try {
            $this->result = FileUtils::handleFile($file);
        } catch (\Exception $exception) {
            $this->error = $exception;
        }
    }

    /**
     * @Then /^il devrait y'avoir eu une erreur$/
     */
    public function ilDevraitYAvoirEuUneErreur()
    {
        if (null === $this->error) {
            throw new \Exception("Expecting an error to happen but everything went good");
        }
    }

    /**
     * @Then /^il ne devrait pas y'avoir eu une erreur$/
     */
    public function ilNeDevraitPasYAvoirEuUneErreur()
    {
        if (null !== $this->error) {
            throw new \Exception("Wasn't expecting an error to happen but went bad : {$this->error->getMessage()}");
        }
    }

    /**
     * @Then /^le message d'erreur devrait être "([^"]*)"(?: et le code (\d+)?)$/
     */
    public function leMessageDErreurDevraitEtre($message, $code = null)
    {
        $code = null === $code ?: intval($code);
        if (null === $this->error || $this->error->getMessage() !== $message) {
            throw new Exception("Expecting error message to be \"{$message}\" but got \"{$this->error->getMessage()}\"");
        }
        if (null !== $code && $this->error->getCode() !== $code) {
            throw new Exception("Expecting error code to be \"{$code}\" but got \"{$this->error->getCode()}\"");
        }
    }
}
