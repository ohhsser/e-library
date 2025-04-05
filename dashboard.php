<?php
session_start();

if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_data'])) {
    header("Location: index.php");
    exit();
}
include 'header.php';
?>

<?php

// require_once __DIR__ . '/vendor/autoload.php';

// use Dotenv\Dotenv;
// use Cloudinary\Configuration\Configuration;
// use Cloudinary\Cloudinary;
// use Cloudinary\Tag\ImageTag;
// use Cloudinary\Transformation\Resize;
// use Cloudinary\Api\Upload\UploadApi;
// use Cloudinary\Api\Exception\ApiError;

// // Load .env file
// $dotenv = Dotenv::createImmutable(__DIR__);
// $dotenv->load();

// $config = new Configuration($_ENV['CLOUDINARY_URL']);
// // Create the Cloudinary instance with the configuration
// $cld = new Cloudinary($config);

// $imageTagString = $cld->image('v1742628761/upload/scn8zupmlsutw6aiervw');
// $imageTag = $cld->ImageTag('v1742628761/upload/scn8zupmlsutw6aiervw');

// echo $imageTagString;
// echo $imageTag;

// $upload = new UploadApi($config);
// $response = $upload->upload('assets/Face.png');

// echo  "<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
?>

<?php include './components/navbar.php'; ?>

<?php
$user_role = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"])[3] : $_SESSION['user_data'][3];
if ($user_role === "user") {
    $allowed_pages = ['home', 'profile', 'books'];
}

if ($user_role === "admin") {
    $allowed_pages = ['home', 'profile', 'books'];
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';


if (!in_array($page, $allowed_pages)) {
    $page = '404';
}
?>

<div id="content">
    <?php include "./pages/{$page}.php"; ?>
</div>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const navLinks = document.querySelectorAll(".nav-link");

        navLinks.forEach(link => {
            link.addEventListener("click", function(event) {
                event.preventDefault();

                const page = this.getAttribute("data-page");

                history.pushState({
                    page
                }, "", `?page=${page}`);
                window.location.replace(`?page=${page}`);
            });
        });


        function updateActiveLink(activePage) {
            navLinks.forEach(link => {
                if (link.getAttribute("data-page") === activePage) {
                    link.classList.add("font-bold");
                } else {
                    link.classList.remove("font-bold");
                }
            });
        }


        window.addEventListener("popstate", function(event) {
            if (event.state && event.state.page) {
                event.preventDefault();
                window.location.replace(`?page=${event.state.page}`);
            }
        });


        const params = new URLSearchParams(window.location.search);
        const currentPage = params.get("page") || "home";
        updateActiveLink(currentPage);
    });
</script>