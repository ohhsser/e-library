<?php

session_start(); // Make sure this is at the very top!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use Composer to manage your PHP library dependency
require __DIR__ . '/../vendor/autoload.php';

// Load .env file
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key' => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
    ],
    'url' => [
        'secure' => true
    ]
]);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['document']['tmp_name'])) {  // Changed 'avatar' to 'document'
        $tempFilePath = $_FILES['document']['tmp_name'];
        $fileType = $_FILES['document']['type'];
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif'
        ];


        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Only PDF and Word documents are allowed.'
            ]);
            exit;
        }

        try {
            $uploadResult = (new UploadApi())->upload($tempFilePath, [
                'folder' => 'upload',
                'resource_type' => 'auto'  // Use 'raw' for non-image files
            ]);

            $uploadedBookUrl = $uploadResult['secure_url'];
            // Store the URL to session
            $_SESSION['uploadedBookUrl'] = $uploadedBookUrl;

            echo json_encode([
                'success' => true,
                'fileUrl' => $uploadResult['secure_url']
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No file provided.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
