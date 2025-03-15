<?php
include './backend/connection.php';

$email = json_decode($_COOKIE["user_data"])[0];
$cur_date = date("y/m/d");

// Initialize all stats
$total_book_count = $total_issued_book_count = $total_reserved_book_count = $total_returned_book_count = 0;
$total_fined_users_count = $total_members_count = $total_users_count = 0;

// Get the first book creation date
$query = "SELECT date FROM books ORDER BY date ASC LIMIT 1";
$result = mysqli_query($con, $query);
$start_date = ($row = mysqli_fetch_assoc($result)) ? $row["date"] : "N/A";

// Get total book count
$query = "SELECT COUNT(*) AS total_books FROM books";
$result = mysqli_query($con, $query);
$total_book_count = ($row = mysqli_fetch_assoc($result)) ? $row['total_books'] : 0;

// Role-based stats
$user_role = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"])[3] : $_SESSION['user_data'][3];

if ($user_role === "user") {
    $total_issued_book_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM issued WHERE user_email='$email'"));
    $total_reserved_book_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM reserved WHERE user_email='$email'"));
    $total_returned_book_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM returned WHERE user_email='$email'"));
}

if ($user_role === "admin") {
    $total_issued_book_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM issued"));
    $total_reserved_book_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM reserved"));
    $total_returned_book_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM returned"));
    $total_fined_users_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM fined_users"));
    $total_members_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM members"));
    $total_users_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM user"));
}
?>


<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6 mt-16">
    <!-- Total Books -->
    <div class="bg-[blanchedalmond] p-6 rounded-lg shadow-md">
        <div class="flex justify-between">
            <span class="text-lg font-semibold">Books</span>
            <i class="fa fa-book text-gray-500"></i>
        </div>
        <div class="pt-3">
            <span class="text-3xl font-bold"><?php echo $total_book_count; ?></span><br />
            <h6 class="text-gray-600">Total Books Available</h6>
            <p class="text-sm text-gray-500">From <?php echo $start_date; ?> - <?php echo "20" . date("y"); ?></p>
        </div>
    </div>

    <!-- Issued Books -->
    <div class="bg-[#bfbee4] p-6 rounded-lg shadow-md">
        <div class="flex justify-between">
            <span class="text-lg font-semibold">Issued Books</span>
            <i class="fa fa-bookmark text-gray-500"></i>
        </div>
        <div class="pt-3">
            <span class="text-3xl font-bold"><?php echo $total_issued_book_count; ?></span>
            <h6 class="text-gray-600">Books Issued</h6>
        </div>
    </div>

    <!-- Reserved Books -->
    <div class="bg-[khaki] p-6 rounded-lg shadow-md">
        <div class="flex justify-between">
            <span class="text-lg font-semibold">Reserved Books</span>
            <i class="fa fa-calendar-check text-gray-500"></i>
        </div>
        <div class="pt-3">
            <span class="text-3xl font-bold"><?php echo $total_reserved_book_count; ?></span>
            <h6 class="text-gray-600">Total Reserved</h6>
        </div>
    </div>

    <!-- Returned Books -->
    <div class="bg-[lightpink] p-6 rounded-lg shadow-md">
        <div class="flex justify-between">
            <span class="text-lg font-semibold">Returned Books</span>
            <i class="fa fa-undo text-gray-500"></i>
        </div>
        <div class="pt-3">
            <span class="text-3xl font-bold"><?php echo $total_returned_book_count; ?></span>
            <h6 class="text-gray-600">Books Returned</h6>
        </div>
    </div>

    <?php if ($user_role === "admin") { ?>
        <!-- Fined Users -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between">
                <span class="text-lg font-semibold">Fined Users</span>
                <i class="fa fa-money-bill text-gray-500"></i>
            </div>
            <div class="pt-3">
                <span class="text-3xl font-bold"><?php echo $total_fined_users_count; ?></span>
                <h6 class="text-gray-600">Users with Fines</h6>
            </div>
        </div>

        <!-- Members Count -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between">
                <span class="text-lg font-semibold">Total Members</span>
                <i class="fa fa-users text-gray-500"></i>
            </div>
            <div class="pt-3">
                <span class="text-3xl font-bold"><?php echo $total_members_count; ?></span>
                <h6 class="text-gray-600">Library Members</h6>
            </div>
        </div>

        <!-- Users Count -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between">
                <span class="text-lg font-semibold">Total Users</span>
                <i class="fa fa-user text-gray-500"></i>
            </div>
            <div class="pt-3">
                <span class="text-3xl font-bold"><?php echo $total_users_count; ?></span>
                <h6 class="text-gray-600">Registered Users</h6>
            </div>
        </div>
    <?php } ?>
</div>