<div
    class="bg-white  w-screen relative flex max-md:flex-col pl-6 max-md:pl-3 pr-3 gap-6 max-md:gap-3 overflow-hidden mt-16">
    <!-- Sidebar -->
    <div
        class="w-64 max-md:w-full max-md:mt-6 md:h-[calc(100vh-5.5rem)] p-6 bg-white shadow-md text-white space-y-2 border-gray-200 border rounded-xl md:fixed md:top-[5rem] md:left-2">
        <h1 class="text-2xl font-bold mb-6 text-black">Profile</h1>
        <button onclick="showSubpage('profile', this)"
            class="w-full text-left text-black p-2 hover:bg-black hover:text-white rounded mb-[5px!important] font-bold bg-black text-white"
            data-route="profile">My Profile</button>
        <?php
        // if ($user_role === "admin") {
        //     echo '<button onclick="showSubpage(\'allProfiles\', this)" 
        //         class="w-full text-left text-black p-2 hover:bg-black hover:text-white rounded mb-[5px!important]" 
        //         data-route="allProfiles">All Users</button>';

        //     echo '<button onclick="showSubpage(\'edit\', this)" 
        //         class="w-full text-left text-black p-2 hover:bg-black hover:text-white rounded mb-[5px!important]" 
        //         data-route="edit">Edit User</button>';
        // }
        ?>


    </div>

    <!-- Main Content -->
    <div class="overflow-y-auto w-full md:ml-64">
        <div class="flex-1 space-y-6 pt-2">
            <div id="explore" class="subpage">
                <!-- <h2 class="text-3xl font-bold mb-4">My Profile</h2> -->
                <?php
                include './components/profileComponent.php';
                ?>
            </div>

            <?php
            // if ($user_role === "admin") {
            //     include './components/upload_book.php';
            //     include './components/edit_book.php';
            // }
            ?>
        </div>
    </div>
</div>