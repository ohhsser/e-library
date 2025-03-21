<?php

include './backend/connection.php';

// Handle file upload
function uploadFile($file)
{
    $targetDir = "uploads/";

    // Check if the uploads directory exists, if not, create it
    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            return "Error: Failed to create upload directory.";
        }
    }

    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    $uploadStatus = $file["error"]; // Check for upload errors

    if ($uploadStatus === UPLOAD_ERR_OK) {

        // Check if the file actually exists
        if (!is_uploaded_file($file["tmp_name"])) {
            return "Error: Possible file upload attack.";
        }

        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return $targetFilePath; // Return the path if upload is successful
        } else {
            return "Error: Failed to move uploaded file. Check folder permissions.";
        }
    } else {
        // Handle different error messages
        switch ($uploadStatus) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "Error: File size is too large.";
            case UPLOAD_ERR_PARTIAL:
                return "Error: The file was only partially uploaded.";
            case UPLOAD_ERR_NO_FILE:
                return "Error: No file was uploaded.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Error: Missing a temporary folder.";
            case UPLOAD_ERR_CANT_WRITE:
                return "Error: Failed to write file to disk.";
            case UPLOAD_ERR_EXTENSION:
                return "Error: File upload stopped by a PHP extension.";
            default:
                return "Error: Unknown upload error.";
        }
    }
}


// Handle book upload
if (isset($_POST['upload'])) {
    $name = $_POST['name'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $published = $_POST['published'];
    $quantity = $_POST['quantity'];
    $rack_no = $_POST['rack_no'];
    $date = date('Y-m-d');

    $src = uploadFile($_FILES['src']);
    echo $src;
    if ($src) {
        $sql = "INSERT INTO books (name, author, category, description, published, src, quantity, rack_no, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssssiss", $name, $author, $category, $description, $published, $src, $quantity, $rack_no, $date);
        $stmt->execute();

        echo "Book uploaded successfully.";
        header("Location: dashboard.php?page=books");
    } else {
        echo "Failed to upload the book cover.";
    }
}

// Handle book editing
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $published = $_POST['published'];
    $quantity = $_POST['quantity'];
    $rack_no = $_POST['rack_no'];

    if (!empty($_FILES['src']['name'])) {
        $src = uploadFile($_FILES['src']);
    }

    $sql = "UPDATE books SET name = ?, author = ?, category = ?, description = ?, published = ?, quantity = ?, rack_no = ?";

    if (isset($src)) {
        $sql .= ", src = ?";
    }

    $sql .= " WHERE id = ?";

    $stmt = $con->prepare($sql);

    if (isset($src)) {
        $stmt->bind_param("sssssisisi", $name, $author, $category, $description, $published, $quantity, $rack_no, $src, $id);
    } else {
        $stmt->bind_param("sssssisi", $name, $author, $category, $description, $published, $quantity, $rack_no, $id);
    }

    $stmt->execute();

    echo '
    <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-gray-100/50">
        <div class="bg-white p-6 rounded shadow-lg text-center space-y-1 w-96">
            <h2 class="text-xl font-bold text-green-600">Success</h2>
            <p>Book updated successfully.</p>
            <button onclick="closeModal()" class="mt-4 bg-black text-white px-4 py-2 rounded w-40">OK</button>
        </div>
    </div>
    <script>
        function closeModal() {
            document.getElementById("successModal").style.display = "none";
        }
    </script>
    ';
    header("Location: dashboard.php?page=books");
}


$subpage = isset($_GET['subpage']) ? $_GET['subpage'] : 'upload';
$user_role = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"])[3] : $_SESSION['user_data'][3];

?>

<div class="bg-white  w-screen relative flex max-md:flex-col pl-6 max-md:pl-3 pr-3 gap-6 overflow-hidden mt-16">
    <!-- Sidebar -->
    <div
        class="w-64 max-md:w-full max-md:mt-6 md:h-[calc(100vh-5.5rem)] p-6 bg-white shadow-md text-white space-y-2 border-gray-200 border rounded-xl md:fixed md:top-[5rem] md:left-2">
        <h1 class="text-2xl font-bold mb-6 text-black">Explore</h1>
        <button onclick="showSubpage('explore', this)"
            class="w-full text-left text-black p-2 hover:bg-black hover:text-white rounded mb-[5px!important] font-bold bg-black text-white"
            data-route="explore">All Books</button>
        <?php
        if ($user_role === "admin") {
            echo '<button onclick="showSubpage(\'upload\', this)" 
                class="w-full text-left text-black p-2 hover:bg-black hover:text-white rounded mb-[5px!important]" 
                data-route="upload">Create Book</button>';

            echo '<button onclick="showSubpage(\'edit\', this)" 
                class="w-full text-left text-black p-2 hover:bg-black hover:text-white rounded mb-[5px!important]" 
                data-route="edit">Edit Book</button>';
        }
        ?>
        <button onclick="showSubpage('borrow', this)"
            class="w-full text-left text-black p-2 hover:bg-black hover:text-white rounded mb-[5px!important]"
            data-route="borrow">Borrowed
            Books</button>

    </div>

    <!-- Main Content -->
    <div class="overflow-y-auto w-full md:ml-64">
        <div class="flex-1 space-y-6 pt-3">
            <!-- All Book Subpage -->
            <div id="explore" class="subpage">
                <h2 class="text-3xl font-bold mb-4">Explore Books</h2>
                <div id="booksContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
            </div>

            <?php
            if ($user_role === "admin") {
                include './components/upload_book.php';
                include './components/edit_book.php';
            }
            ?>

            <!-- Borrow Book Subpage -->
            <div id="borrow" class="subpage hidden">
                <h2 class="text-3xl font-bold mb-4">Borrowed Books</h2>
                <div id="borrowedContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
            </div>

        </div>
    </div>
</div>

<script>
    function showSubpage(pageId, button) {
        // Show the selected subpage
        const subpages = document.querySelectorAll('.subpage');
        subpages.forEach(page => page.classList.add('hidden'));
        document.getElementById(pageId).classList.remove('hidden');

        // Make the selected button text bold
        const buttons = document.querySelectorAll('[data-route]');
        buttons.forEach(btn => btn.classList.remove('font-bold'));
        buttons.forEach(btn => btn.classList.remove('bg-black'));
        buttons.forEach(btn => btn.classList.remove('text-white'));
        button.classList.add('font-bold');
        button.classList.add('text-white');
        button.classList.add('bg-black');
    }

    async function fetchBooks() {
        try {
            const response = await fetch('/backend/fetch_books.php'); // Your PHP file URL
            const books = await response.json()
                .then(books => {
                    const booksContainer = document.getElementById('booksContainer');
                    const email = `<?php $user_email = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"], true)[0] : null;
                    echo $user_email; ?>`

                    const role = `<?php $user_role = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"])[3] : $_SESSION['user_data'][3];
                    echo $user_role; ?>`

                    const bookButton = (book, email) => {
                        const button = document.createElement("button");
                        button.className = "px-4 py-1 rounded text-sm transition view-btn text-white bg-black hover:bg-black/50";

                        if (role === "admin") {
                            button.innerText = "Edit Book";
                            button.setAttribute("onclick", "showSubpage('edit', this)");
                            button.setAttribute("data-route", "edit");
                        } else {
                            button.innerText = "Borrow";
                            button.setAttribute("data-id", book.id);
                            button.setAttribute("data-name", book.name);
                            button.setAttribute("data-author", book.author);
                            button.setAttribute("data-category", book.category);
                            button.setAttribute("data-description", book.description);
                            button.setAttribute("data-published", book.published);
                            button.setAttribute("data-quantity", book.quantity);
                            button.setAttribute("data-rack", book.rack_no);
                            button.setAttribute("onclick", `reserveBook(${book.id}, '${book.name}', '${email}', '${book.src}')`);
                        }

                        return button;
                    }
                    booksContainer.innerHTML = books.map(book => `
                    <div class="relative w-full h-0 pb-[56.25%] bg-white shadow-md rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="absolute inset-0 flex flex-col justify-between p-2 max-md:p-4">
                            <div>
                                <h4 class="text-lg max-md:text-sm font-bold text-gray-800">${book.name}</h4>
                                <p class="text-sm text-gray-600">Author: ${book.author}</p>
                                <p class="text-sm text-gray-600">Category: ${book.category}</p>
                            </div>
                            <div class="flex justify-between items-center md:mt-2">
                                <p class="text-sm text-gray-500">Published: ${book.published}</p>
                                <div id="button-container-${book.id}"></div>
                            </div>
                        </div>
                    </div>
                `).join('');

                    // Append the buttons after rendering the HTML
                    books.forEach(book => {
                        const buttonContainer = document.getElementById(`button-container-${book.id}`);
                        if (buttonContainer) {
                            const buttonElement = bookButton(book, email);
                            buttonContainer.appendChild(buttonElement);
                        }
                    });
                })

            document.addEventListener('DOMContentLoaded', () => {
                const bookSelect = document.getElementById('bookSelect');
                if (bookSelect) {
                    bookSelect.innerHTML = '<option value="">Select a Book</option>';
                    books?.forEach(book => {
                        const option = document.createElement('option');
                        option.value = book.id;
                        option.textContent = book.name;
                        bookSelect.appendChild(option);
                    });
                }
            });

            // Save books globally for later use
            window.existingBooks = books;
        } catch (error) {
            console.error('Error fetching books:', error);
        }
    }

    // Call the function to fetch books on page load
    fetchBooks();

    async function fetchBorrowedBooks() {
        try {
            const response = await fetch('/backend/fetch_borrowed_books.php'); // Your PHP file URL
            const borrowedBooks = await response.json()
                .then(books => {
                    const borrowedContainer = document.getElementById('borrowedContainer');

                    // Render books
                    borrowedContainer.innerHTML = books.map(book =>
                        book.book_details.map(bookDetail => `
                <div class="relative w-full h-0 pb-[56.25%] bg-white shadow-md rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow mt-4">
                    <div class="absolute inset-0 flex flex-col justify-between p-2 max-md:p-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">${bookDetail.name}</h4>
                            <p class="text-sm text-gray-600">Author: ${bookDetail.author}</p>
                            <p class="text-sm text-gray-600">Category: ${bookDetail.category}</p>
                        </div>
                        <div class="flex justify-between items-center md:mt-2">
                            <p class="text-sm text-gray-500">Published: ${bookDetail.published}</p>
                           <button 
                            class="bg-black text-white px-4 py-1 rounded text-sm hover:bg-black/50 transition delete-btn"
                            data-id="${bookDetail.id}"
                            data-name="${bookDetail.name}"
                            data-author="${bookDetail.author}"
                            data-category="${bookDetail.category}"
                            data-description="${bookDetail.description}"
                            data-published="${bookDetail.published}"
                            data-quantity="${bookDetail.quantity}"
                            data-rack="${bookDetail.rack_no}"
                            onclick="deleteReservedBook(${bookDetail.id})"
                        >Delete</button>
                        </div>
                    </div>
                </div>
            `).join('')
                    ).join('');
                });

            // Save books globally for later use
            window.borrowedBooks = borrowedBooks;
        } catch (error) {
            console.error('Error fetching borrowed books:', error);
        }
    }

    // Call the function to fetch books on page load
    fetchBorrowedBooks();

    function Delete(id) {
        confirm("Are you sure want to delete ", id);
        $.ajax({
            type: "POST",
            url: "./backend/deleteBook.php",
            data: {
                id: id
            },
            success: function (data) {
                window.location.replace(`?page=books`);
                showSubpage("borrow", this)
            },
            error: function () {
                toastr.error("Failed to delete")
            }
        })
    }
    //delete reserved books
    function deleteReservedBook(id) {
        console.log(id)
        $.ajax({
            method: "POST",
            url: "./backend/deleteReservedBook.php",
            data: {
                id: id
            },
            success: function (res) {

                toastr.success("Successfully deleted.")
                fetchBorrowedBooks();
            }
        })
    }

    function reserveBook(book_id, bookname, user_email, src) {
        //send parameter to the backend
        $.ajax({
            type: "POST",
            url: "./backend/reserveBooks.php",
            data: {
                user_email: user_email,
                book_id: book_id,
                bookname: bookname,
                src: src
            },
            success: function (data) {
                if (data === "success") {
                    toastr.success("Book successfully reserved.");
                    fetchBooks();
                    fetchBorrowedBooks();
                } else if (data === "found") {
                    toastr.error("Selected book already reserved.")
                } else {
                    toastr.error("500!!.  Failed to reserved book.");
                }
            },
            error: function () {
                toastr.error("404!!. Client side error.")
            }
        })
    }

</script>