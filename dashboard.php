<?php
session_start();

if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_data'])) {
    header("Location: index.php");
    exit();
}
include 'header.php';
?>

<?php include './components/navbar.php'; ?>

<?php

$allowed_pages = ['home', 'profile', 'books'];


$page = isset($_GET['page']) ? $_GET['page'] : 'home';


if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}
?>

<div id="content">
    <?php include "./pages/{$page}.php"; ?>
</div>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const navLinks = document.querySelectorAll(".nav-link");

        navLinks.forEach(link => {
            link.addEventListener("click", function (event) {
                event.preventDefault();

                const page = this.getAttribute("data-page");

                history.pushState({ page }, "", `?page=${page}`);

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


        window.addEventListener("popstate", function (event) {
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