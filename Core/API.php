<?php

namespace Core;

use Dotenv\Dotenv;
use Core\Form;

class API {
    private static $externApi = 'https://havoc.app/api/v1/drm';

    public static function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

            if ($contentType === "application/json") {
                self::processJsonRequest();
            } else {
                self::respondWithError('Unsupported Content-Type', 415);
            }
        }
    }

    private static function processJsonRequest() {
        $jsonData = file_get_contents("php://input");
        $data = json_decode ($jsonData, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            self::respondWithError('Invalid JSON data', 400);
        }

        $udid = isset($data['udid']) ? trim($data['udid']) : null;
        $modelID = isset($data['model']) ? trim($data['model']) : null;
        $packageID = isset($data['identifier']) ? trim($data['identifier']) : null;

        $formData = [
            'udid' => $udid,
            'model' => $modelID,
            'identifier' => $packageID
        ];

        if (Form::empty($formData)) {
            self::respondWithError('Missing parameters', 400);
        }

        self::sendRequestToExternalApi($udid, $modelID, $packageID);
    }

    private static function authorizeRequest($udid) {
        http_response_code(200);
        echo json_encode([
            'status' => 'completed'
        ]);
    }

    private static function getToken($packageID) {
        return $_ENV[strtoupper($packageID) . ".TOKEN"] ?? null;
    }

    private static function sendRequestToExternalApi($udid, $modelID, $packageID) {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $ch = curl_init(self::$externApi);
        $data = [
            'udid' => $udid,
            'model' => $modelID,
            'identifier' => $packageID,
            'token' => self::getToken($packageID)
        ];

        if ($data['token'] === null) {
            self::respondWithError('Invalid identifier', 400);
        }

        $payload = json_encode($data);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);
        
        header('Content-Type: application/json');

        $resultArray = json_decode($result, true);

        if (isset($resultArray) && $resultArray['status'] == 'completed') {
            self::authorizeRequest($udid);
            exit();
        }

        echo json_encode(['error' => 'Request failed']);
        exit();
    }

    private static function respondWithError($message, $statusCode) {
        $json = [
            'status' => 'error',
            'message' => $message
        ];

        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($json);
        exit();
    }
}