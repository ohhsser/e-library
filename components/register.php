<?php
session_start();
?>

<div class="outline-none bg-black flex justify-center items-center h-screen">
    <div class="outline-none w-full max-w-md bg-white p-6 rounded-lg shadow-md">
        <h2 class="outline-none text-2xl font-semibold text-center mb-4">Create an Account</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="outline-none text-red-500 text-sm"><?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="text-green-500 text-sm"><?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <form action="./backend/register_action.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required
                class="outline-none w-full p-2 border rounded mb-2">
            <input type="email" name="email" placeholder="Email" required
                class="outline-none w-full p-2 border rounded mb-2">
            <input type="phone" name="phone" placeholder="Phone" required
                class="outline-none w-full p-2 border rounded mb-2">
            <input type="password" name="password" placeholder="Password" required
                class="outline-none w-full p-2 border rounded mb-2">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required
                class="outline-none w-full p-2 border rounded mb-2">

            <select name="role" required class="outline-none w-full p-2 border rounded mb-2">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" name="register" class="outline-none w-full bg-black text-white py-2 rounded">Sign
                Up</button>
        </form>

        <p class="outline-none text-center text-sm mt-4">Already have an account? <a href="index.php"
                class="outline-none text-blue-500">Login</a>
        </p>
    </div>
</div>