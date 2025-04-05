<?php

include './backend/connection.php';

// Check if user_data cookie exists and retrieve the user email
$user_email = null;
if (isset($_COOKIE["user_data"])) {
    $user_data = json_decode($_COOKIE["user_data"], true);
    if (isset($user_data[0])) {
        $user_email = $user_data[0];
    }
}

$role = isset($_SESSION['user_data'][3]) ? $_SESSION['user_data'][3] : null;

// Redirect if no user is found
if (!$user_email) {
    header("Location: index.php");
    exit();
}

// Check if table exists
$selectedTable = ($role === "user") ? "user" : "admin";
$query = "SELECT * FROM `$selectedTable` WHERE email = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If not found in 'user', check 'admin'
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

<div id="Profile"
    class="my-2 rounded-xl border border-stone-200 max-md:w-full bg-white text-stone-950 shadow-none flex flex-col">
    <div class="flex flex-col space-y-1.5 p-3">
        <h3 class="font-semibold leading-none tracking-tight">Profile</h3>
        <p class="text-sm text-stone-500">Modify your account's profile information here</p>
    </div>
    <div class="p-3 pt-0 flex flex-col flex-1">
        <form class="w-full" id="profile-form" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-3">
                <!-- Account Name -->
                <div class="grid gap-3">
                    <label class="text-sm font-medium">Id</label>
                    <input type="text" name="id"
                        class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                        value="<?php echo $user['id']; ?>" ... readonly>
                </div>
                <!-- Username -->
                <div class="grid gap-3">
                    <label class="text-sm font-medium">Account Name</label>
                    <input type="text" name="name"
                        class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                        value="<?php echo $user['name']; ?>" ... readonly>
                </div>

                <!-- Email -->
                <div class="grid gap-3">
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email"
                        class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                        value="<?php echo $user['email']; ?>">
                </div>

                <!-- Phone Number -->
                <div class="grid gap-3">
                    <label class="text-sm font-medium">Phone Number</label>
                    <input type="text" name="phone" autocomplete="phone"
                        class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                        value="<?php echo $user['phone'] ?? ''; ?>">
                </div>

                <!-- Current Password -->
                <div class="grid md:col-span-2 gap-3">
                    <label class="text-sm font-medium">Current Password</label>
                    <input type="password" name="current_password" autocomplete="password"
                        class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                        placeholder="Current password">
                </div>

                <!-- New Password -->
                <div class="grid md:col-span-2 gap-3">
                    <label class="text-sm font-medium">New Password</label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                        placeholder="New password">
                </div>
            </div>
            <!-- Account src Update -->
            <div class="w-full space-y-5 mt-10">
                <div>
                    <label class="text-sm font-medium">Account Image Update</label>
                    <p class="text-xs text-gray-400">Update the profile Image of your account</p>
                </div>
                <div id="uploadForm">
                    <div class="flex space-x-4">
                        <div class="cursor-pointer relative">
                            <div class="relative overflow-hidden rounded-full w-24 h-24">
                                <img id="avatarPreview" src="<?php echo $user['src'] ?? 'https://via.placeholder.com/92'; ?>"
                                    alt="User Avatar" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div class="w-full md:w-52 space-y-4 absolute">
                            <input type="file" name="avatar" accept="image/*" id="avatarInput" class="w-24 h-24 bg-black opacity-0">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Save Changes Button -->
            <div class="mt-5">
                <button type="submit" class="w-full px-4 py-2 bg-stone-950 text-white rounded-md">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php
if ($role === "admin") {
    echo '
    <div>
        <div
            class="mb-2 rounded-xl border border-stone-200 max-md:w-full bg-white text-stone-950 shadow-none flex flex-col">
            <div class="flex flex-col space-y-1.5 p-3">
                <h3 class="font-semibold leading-none tracking-tight">Administrators</h3>
                <p class="text-sm text-stone-500">Manage users here</p>
            </div>
            <div class="p-3 pt-0 flex flex-col flex-1">
                <div class="grid grid-cols-1 gap-5 mb-3">
                    <div class="w-full grid gap-3 mb-5">
                        <label class="text-sm font-medium leading-none">Create new user</label>
                        <form id="createUserForm" class="w-full grid grid-cols-2 gap-3 items-center">
                            <input type="text"
                                class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                                placeholder="Account Name" name="name" />
                            <input type="text"
                                class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                                placeholder="Email" name="email" />
                            <input type="text "
                                class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                                placeholder="Phone Number" name="phone" />
                            <input type="password" name="password"
                                class="h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm"
                                placeholder="Password" />
                                 <select name="role" required class="outline-none h-9 w-full rounded-md border border-stone-200 bg-transparent px-3 py-1 text-sm col-span-2">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            <button type="submit"
                                class=" col-span-2 w-full px-4 py-2 bg-stone-950 text-white rounded-md">Create
                                Account</button>
                        </form>
                    </div>
                    <div class="relative max-h-96 overflow-y-scroll">
                        <table class="w-full text-sm">
                            <thead class="sticky top-0 bg-white">
                                <tr class="border-b">
                                    <th class="h-12 text-left">Name</th>
                                    <th class="h-12 text-left">Email</th>
                                    <th class="h-12 text-left">Role</th>
                                    <th class="h-12 text-right"></th>
                                </tr>
                            </thead>
                            <tbody id="for-user">
    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    ';
}
?>

<script>
    let uploadedImageUrl = "";

    const loadUsers = async () => {
        try {
            const response = await fetch('/backend/user_management.php');

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const users = await response.json();
            const userTableBody = document.getElementById('for-user');

            if (!userTableBody) return;

            // Generate rows efficiently and update once
            userTableBody.innerHTML = users.map(user => `
            <tr class="border-b border-gray-100">
                <td class="py-4">${user.name}</td>
                <td class="py-4">${user.email}</td>
                <td class="py-4">${user.role}</td>
                <td class="py-4 text-right">
                    <button 
                        class="delete-user h-8 px-3 rounded-md bg-red-600 text-white text-xs"
                        data-id="${user.id}"
                        data-email="${user.email}"
                        data-role="${user.role}"
                    >Delete</button>
                </td>
            </tr>
        `).join('');

            // Attach event listeners after table update
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', deleteUser);
            });

        } catch (error) {
            console.error("Error loading users:", error);
        }
    };


    const deleteUser = async (event) => {
        const userId = event.target.dataset.id;
        const email = event.target.dataset.email;
        const role = event.target.dataset.role;
        const response = await fetch('/backend/user_management.php', {
            method: 'DELETE',
            body: new URLSearchParams({
                id: userId,
                email: email,
                role: role
            })
        });
        const result = await response.json();

        if (result.status === 'success') {
            toastr.success(result.message);
            loadUsers();
        } else {
            toastr.error(result.message);
        }
    };

    const createUser = async (userData) => {
        try {
            const response = await fetch('/backend/user_management.php', {
                method: 'POST',
                body: userData
            });

            const result = await response.json();

            if (result.status === 'success') {
                toastr.success(result.message);
                loadUsers();
            } else {
                toastr.error(result.message);
            }
        } catch (error) {
            toastr.error('An error occurred: ' + error.message);
        }
    };

    const updateUser = async (userData) => {
        try {
            if (uploadedImageUrl) {
                userData.append('avatarUrl', uploadedImageUrl);
            }
            const role = `<?php $user_role = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"])[3] : $_SESSION['user_data'][3];
                                    echo $user_role; ?>`
            // Append a hidden field to indicate the request type
            userData.append('_method', 'PUT');
            userData.append('role', role);

            const response = await fetch('/backend/user_management.php', {
                method: 'POST', // Use POST instead of PUT
                body: userData
            });

            const result = await response.json();

            if (result.status === 'success') {
                toastr.success(result.message);
                loadUsers();
            } else {
                toastr.error(result.message);
            }
        } catch (error) {
            toastr.error('An error occurred: ' + error.message);
        }
    };

    document?.querySelector('#createUserForm')?.addEventListener('submit', (event) => {
        event.preventDefault();
        console.log("yes")
        const formData = new FormData(event.target);

        if (formData.get('password').length <= 5) {
            toastr.error('Password must be more than 5 characters');
            return;
        }

        createUser(formData);
    });

    document.querySelector('#profile-form').addEventListener('submit', (event) => {
        event.preventDefault();
        const formData = new FormData(event.target);

        if (formData.get('current_password') !== "" && formData.get('password') !== "" && formData.get('password') === formData.get('current_password') && formData.get('password').length <= 5 && formData.get('current_password').length <= 5) {
            toastr.error('Password must be more than 5 characters');
            return;
        }

        updateUser(formData);
    });

    document?.getElementById('avatarInput').addEventListener('change', function(event) {
        const fileInput = event.target;
        const formData = new FormData();
        formData.append('avatar', fileInput.files[0]);
        console.log(formData)
        console.log(fileInput.files[0])
        $.ajax({
            type: "POST",
            url: "./backend/imageUpload.php",
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content-type header
            success: function(data) {
                try {
                    if (data.success) {
                        uploadedImageUrl = data.imageUrl;
                        document.getElementById('avatarPreview').src = data.imageUrl;
                        toastr.success("Image uploaded successfully.");
                    } else {
                        toastr.error(data.message || "Failed to upload image.");
                    }
                } catch (error) {
                    toastr.error("Invalid response from server.");
                }
            },
            error: function() {
                toastr.error("404!! Client-side error.");
            }
        });
    });

    loadUsers();
</script>

<script>
</script>