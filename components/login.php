<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="flex justify-center items-center h-screen w-screen bg-[#fff]/50">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-sm max-md:w-[95%] self-center border border-gray-200">
        <h2 class="text-2xl font-semibold text-center mb-4">Login</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-500 text-sm"><?php echo $_SESSION['error'];
                                            unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="./backend/auth.php" method="POST">
            <input type="email" name="email" placeholder="Email" required
                class="outline-none w-full p-2 border rounded mb-2">
            <input type="password" name="password" placeholder="Password" required
                class="outline-none w-full p-2 border rounded mb-2">
            <select name="role" id="role" class="outline-none w-full p-2 border rounded mb-2">
                <option value="" disabled selected>Select a Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="login"
                class="outline-none w-full bg-black text-white py-2 rounded">Login</button>
        </form>
        <br>
        <p class="outline-none text-center text-sm mt-4">New user? <a href="createaccount.php"
                class="outline-none text-blue-500 underline">Create Account</a>
        </p>
    </div>
</div>