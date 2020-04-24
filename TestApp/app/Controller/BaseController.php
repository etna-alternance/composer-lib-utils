<?php

namespace TestApp\Controller;

use ETNA\Utils\Services\CsvService;
use ETNA\Utils\Services\FileService;
use ETNA\Utils\Services\LoginService;
use ETNA\Utils\Services\NotifyService;
use ETNA\Utils\Services\PasswordService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends AbstractController
{
    /**
     * @Route("/csv", methods={"POST"}, name="postCSV")
     */
    public function postCSV(Request $req, CsvService $service)
    {
        $datas = array_map(
            function ($line) use ($service) {
                return $service->getTokenFromArray($line);
            },
            $req->request->all()
        );

        return $this->json([
            "csv" => $service->arrayToCsv($datas)
        ], 200);
    }

    /**
     * @Route("/sputcsv", methods={"GET"}, name="getSPutCsv")
     */
    public function getSPutCsv(Request $req, CsvService $service)
    {
        $params = array_merge(
            [
                "delimiter" => ';',
                "enclosure" => '"',
                "eol"       => "\n"
            ],
            $req->query->all()
        );
        $datas = [
            "id"   => 42,
            "name" => "ouais ouais"
        ];

        return $this->json([
            "csv" => $service->sputcsv($datas, $params['delimiter'], $params['enclosure'], $params['eol']),
        ], 200);
    }

    /**
     * @Route("/file", methods={"POST"}, name="postFile")
     */
    public function postFile(Request $req, FileService $service)
    {
        $file = $req->files->get("file");

        return $this->json([
            "content" => $service->handleFile($file)
        ], 200);
    }

    /**
     * @Route("/login", methods={"GET"}, name="getLogin")
     */
    public function getLogin(Request $req, LoginService $service)
    {
        $firstname = $req->query->get("firstname", '');
        $lastname  = $req->query->get("lastname", '');

        return $this->json([
            "login" => $service->generate($lastname, $firstname)
        ], 200);
    }

    /**
     * @Route("/password", methods={"GET"}, name="getPassword")
     */
    public function getPassword(Request $req, PasswordService $service)
    {
        $password  = $service->generate();
        $encrypted = $service->encrypt($password);

        return $this->json([
            "password"  => $password,
            "encrypted" => $encrypted
        ], 200);
    }

    /**
     * @Route("/mail", methods={"POST"}, name="sendMail")
     */
    public function sendMail(Request $req, NotifyService $service)
    {
        $files = $req->files->all();
        $datas = array_merge([
            "title"     => "",
            "template"  => "",
            "from"      => "",
            "to"        => "",
            "mail_data" => [],
            "options"   => []
        ], $req->request->all());

        if (!empty($files)) {
            $datas["options"]["files"] = $service->prepareFilesForMail(
                array_map(
                    function (UploadedFile $file) {
                        return ["name" => $file->getClientOriginalName(), "path" => $file->getPathname()];
                    },
                    $files
                )
            );
        }

        $service->sendMail($datas["title"], $datas["template"], $datas["from"], $datas["to"], $datas["mail_data"], $datas["options"]);

        return $this->json("OK", 200);
    }

    /**
     * @Route("/print", methods={"POST"}, name="sendPrint")
     */
    public function sendPrint(Request $req, NotifyService $service)
    {
        $template = $req->files->get("template", null);
        $datas    = array_merge([
            "title"         => "",
            "template"      => "",
            "routing_key"   => null,
            "sprinter_data" => [],
            "options"       => []
        ], $req->request->all());

        if (null !== $template) {
            $datas["title"]    = $template->getClientOriginalName();
            $datas["template"] = file_get_contents($template->getPathname());
        }

        $service->sendPrint($datas["title"], $datas["template"], $datas["routing_key"], $datas["sprinter_data"], $datas["options"]);

        return $this->json("OK", 200);
    }
}
