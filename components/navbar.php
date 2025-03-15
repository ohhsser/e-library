<div class="bg-white text-black fixed w-full top-0 z-50">
    <nav class="w-full bg-white text-black p-4 shadow-md ">
        <div class="w-full px-3 mx-auto flex justify-between items-center">
            <!-- Logo -->
            <a href="./dashboard.php" class="text-xl font-semibold">Library</a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex space-x-6 items-center">
                <a class="nav-link cursor-pointer <?= $page == 'home' ? 'font-bold' : '' ?>" data-page="home">Home
                </a>
                <a class="nav-link cursor-pointer <?= $page == 'books' ? 'font-bold' : '' ?>" data-page="books">Books
                    <a class="nav-link cursor-pointer <?= $page == 'profile' ? 'font-bold' : '' ?>"
                        data-page="profile">Profile</a>
                </a>
                <a>
                    <form id="logout-form" action="./backend/logout.php" method="POST">
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md text-sm font-medium">
                            Logout
                        </button>
                    </form>
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="menu-btn" class="md:hidden text-xl focus:outline-none z-50">
                ☰
            </button>
        </div>

        <!-- Mobile Dropdown -->
        <div id="mobile-menu" class="hidden md:hidden p-4">
            <a href="#"
                class="nav-link cursor-pointer block py-2 hover:text-gray-400 text-decoration-none <?= $page == 'home' ? 'font-bold' : '' ?>"
                data-page="home">Home</a>
            <a href="#"
                class="nav-link cursor-pointer block py-2 hover:text-gray-400 text-decoration-none <?= $page == 'books' ? 'font-bold' : '' ?>"
                data-page="books">Books</a>
            <a href="#"
                class="nav-link cursor-pointer block py-2 hover:text-gray-400 text-decoration-none <?= $page == 'profile' ? 'font-bold' : '' ?>"
                data-page="profile">Profile</a>

            <form id="mobile-logout-form" action="./backend/logout.php" method="POST">
                <button type="submit" class="text-red-600 hover:text-red-700 py-2 font-medium">
                    Logout
                </button>
            </form>
        </div>
    </nav>
</div>