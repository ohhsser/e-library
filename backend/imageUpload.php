<?php

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
    if (isset($_FILES['avatar']['tmp_name'])) {
        $tempFilePath = $_FILES['avatar']['tmp_name'];

        try {
            $uploadResult = (new UploadApi())->upload($tempFilePath, [
                'folder' => 'upload',
                'resource_type' => 'image' // Corrected from 'video' to 'image'
            ]);

            echo json_encode([
                'success' => true,
                'imageUrl' => $uploadResult['secure_url']
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
            'message' => 'No image file provided.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
