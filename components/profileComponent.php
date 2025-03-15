<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './backend/connection.php';

// Check if user_data cookie exists
$user_email = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"], true)[0] : null;
$role = $_SESSION['user_data'][3];

// Redirect if no user is found
if (!$user_email) {
    header("Location: index.php");
    exit();
}

// Check if 'user' table exists
$query = "SELECT * FROM user WHERE email = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If not found in 'users', check 'admin'
if (!$user) {
    $query = "SELECT * FROM admin WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

// If user is still not found, redirect
if (!$user) {
    header("Location: index.php");
    exit();
}

?>


<div class="flex justify-center items-center w-full h-[calc(100vh-70px)] bg-white mt-16">
    <!-- Sidebar -->
    <!-- <aside class="w-64 bg-white shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800">Settings</h2>
        <ul class="mt-4 space-y-2">
            <li><a href="#" class="block text-gray-600 hover:text-blue-500">Profile</a></li>
            <li><a href="#" class="block text-gray-600 hover:text-blue-500">Security</a></li>
            <li><a href="#" class="block text-gray-600 hover:text-blue-500">Notifications</a></li>
        </ul>
    </aside> -->

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <div class="max-w-3xl mx-auto bg-white border-[0.5px] rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 border-b pb-4">Profile Settings</h2>

            <div class="flex items-center space-x-6 mt-6 ">
                <!-- Profile Picture -->
                <div class="relative  ">
                    <img src="<?php echo $user['src'] ? $user['src'] : 'default-avatar.png'; ?>" alt="Profile Picture"
                        class="w-28 h-28 rounded-full border-4 border-gray-200 shadow-sm">
                    <!-- <label for="profile-pic"
                        class="absolute bottom-0 right-6 bg-blue-500 text-white p-2 rounded-full cursor-pointer">
                        <i class="fa fa-camera"></i>
                    </label> -->
                    <input type="file" id="profile-pic" class="opacity-0 absolute h-28 w-28 top-0 cursor-pointer"
                        placeholder="">
                </div>

                <!-- User Info -->
                <div>
                    <p class="text-xl font-semibold"><?php echo htmlspecialchars($user["name"]); ?></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($role); ?></p>
                </div>
            </div>

            <!-- Profile Form -->
            <form class="mt-6 space-y-4">
                <div>
                    <label class="block text-gray-700">Full Name</label>
                    <input type="text" value="<?php echo htmlspecialchars($user["name"]); ?>"
                        class="w-full p-2 border rounded focus:ring focus:ring-black">
                </div>

                <div>
                    <label class="block text-gray-700">Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user["email"]); ?>" disabled
                        class="w-full p-2 border rounded bg-gray-100 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-gray-700">Phone</label>
                    <input type="text"
                        value="<?php echo isset($user["phone"]) ? htmlspecialchars($user["phone"]) : 'N/A'; ?>"
                        class="w-full p-2 border rounded focus:ring focus:ring-black">
                </div>

                <div>
                    <label class="block text-gray-700">Joined</label>
                    <input type="text" value="<?php echo $user['date']; ?>" disabled
                        class="w-full p-2 border rounded bg-gray-100 cursor-not-allowed">
                </div>

                <div class="flex justify-between mt-6">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                        Save Changes
                    </button>

                    <form id="logout-form" action="./backend/logout.php" method="POST">
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</div>