<?php

include './backend/connection.php';
session_start(); // Ensure the session is started
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle book upload
if (isset($_POST['upload'])) {
    $name = $_POST['name'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $published = $_POST['published'];
    $src = isset($_SESSION['uploadedBookUrl']) ? $_SESSION['uploadedBookUrl'] : "";
    unset($_SESSION['uploadedBookUrl']); // Clear session value after retrieving it

    $quantity = $_POST['quantity'];
    $rack_no = $_POST['rack_no'];
    $date = date('Y-m-d');

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

    if (isset($_SESSION['uploadedBookUrl'])) {
        $src =  $_SESSION['uploadedBookUrl'] ?? "";
        unset($_SESSION['uploadedBookUrl']); // Clear session value after retrieving it
    }

    $sql = "UPDATE books SET name = ?, author = ?, category = ?, description = ?, published = ?, quantity = ?, rack_no = ?";

    if (isset($src)) {
        $sql .= ", src = ?";
    }

    $sql .= " WHERE id = ?";

    $stmt = $con->prepare($sql);

    if (isset($src)) {
        $stmt->bind_param("sssissisi", $name, $author, $category, $description, $published, $quantity, $rack_no, $src, $id);
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
<style>
    #spinner-overlay {
        display: none;
        /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* Semi-transparent black */
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    #spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #000;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<div class="bg-white  w-screen relative flex max-md:flex-col pl-6 max-md:pl-3 pr-3 gap-6 overflow-hidden mt-16">
    <!-- Sidebar -->
    <div id="spinner-overlay">
        <div id="spinner"></div>
    </div>

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
                echo '
                <div id="upload" class="subpage hidden">
                <h2 class="text-3xl font-bold mb-4">Upload Book</h2>
                <!-- Upload Book Form -->
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="text" name="name" placeholder="Book Name" required class="w-full p-3 border rounded">
                    <input type="text" name="author" placeholder="Author" required class="w-full p-3 border rounded">
                    <input type="text" name="category" placeholder="Category" required class="w-full p-3 border rounded">
                    <textarea name="description" placeholder="Description" required
                        class="w-full p-3 border rounded min-h-54"></textarea>
                    <input type="date" name="published" placeholder="Published (e.g., 2025)" required
                        class="w-full p-3 border rounded">
                    <div class="w-full relative overflow-hidden">
                        <label for="book-upload" id="file-label" class="block w-full p-3 border rounded cursor-pointer text-left bg-white hover:bg-gray-100">
                            Choose a File
                        </label>
                        <input type="file" id="book-upload" name="src" required class="opacity-0 absolute top-0" onchange="updateFileName(this)">
                    </div>

                    <input type="number" name="quantity" placeholder="Quantity" required class="w-full p-3 border rounded">
                    <input type="text" name="rack_no" placeholder="Rack No" required class="w-full p-3 border rounded">
                    <button type="submit" name="upload" id="upload" class="w-full bg-black text-white p-3 rounded">Upload
                        Book</button>
                </form>
            </div> 

                <div id="edit" class="subpage hidden ">
                <h2 class="text-3xl font-bold mb-4">Edit Book</h2>
                <!-- Edit Book Form (Modify as needed) -->
                <form action="" class="space-y-4" enctype="multipart/form-data" method="POST">
                    <select name="id" id="bookSelect" class="w-full p-3 border rounded" required>
                        <option value="">Select a Book</option>
                        <!-- Options will be populated dynamically -->
                    </select>
                    <input type="text" name="name" id="name" placeholder="Book Name" class="w-full p-3 border rounded">
                    <input type="text" name="author" id="author" placeholder="Author" class="w-full p-3 border rounded">
                    <input type="text" name="category" id="category" placeholder="Category" class="w-full p-3 border rounded">
                    <textarea name="description" id="description" placeholder="Description"
                        class="w-full p-3 border rounded min-h-54"></textarea>
                    <input type="text" name="published" id="published" placeholder="Published (e.g., 2025)"
                        class="w-full p-3 border rounded">
                    <div class="w-full relative overflow-hidden">
                        <label for="book-upload-edit" id="edit-label" class="block w-full p-3 border rounded cursor-pointer text-left bg-white hover:bg-gray-100">
                            Choose a File
                        </label>
                        <input type="file" id="book-upload-edit" name="src" class="opacity-0 absolute top-0" onchange="editFileName(this)">
                    </div>
                    <input type="number" name="quantity" id="quantity" placeholder="Quantity" class="w-full p-3 border rounded">
                    <input type="text" name="rack_no" id="rack_no" placeholder="Rack No" class="w-full p-3 border rounded">
                    <button type="submit" name="edit" id="edit-button" class="w-full bg-black text-white p-3 rounded">Edit Book</button>
                </form>
            </div>';
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

 const deleteBookButton = (book, email) => {
                        const button = document.createElement("button");
                        button.className = "px-4 py-1 rounded text-sm transition view-btn text-white bg-black hover:bg-black/50";

                        if (role === "admin") {
                            button.innerText = "Delete Book";
                            button.setAttribute("onclick", `Delete(${book.id})`);
                            return button;
                        }
                    }
                    
                    const bookButton = (book, email) => {
                        const button = document.createElement("button");
                        button.className = "px-4 py-1 rounded text-sm transition view-btn text-white bg-black hover:bg-black/50";

                        if (role === "admin") {
                            button.innerText = "Edit Book";
                            button.setAttribute("onclick", `populateEditForm(${JSON.stringify(book)})`);
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
                    <div class="relative w-full h-0 pb-[56.25%] bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow" >
                        <div class="absolute inset-0 flex flex-col justify-between p-2 max-md:p-4">
                            <div class="flex flex-row justify-between items-center">
                            <div>
                                <h4 class="text-lg max-md:text-sm font-bold text-gray-800">${book.name}</h4>
                                <p class="text-sm text-gray-600">Author: ${book.author}</p>
                                <p class="text-sm text-gray-600">Category: ${book.category}</p>
                                <p class="text-sm text-gray-600">Copies Left: ${book.quantity}</p>
                            </div>
                            <img src="${book.src}" alt="${book.src}" class="aspect-[9/16] h-28 rounded mr-6 object-cover">
                            </div>
                            <div class="flex justify-between items-end md:mt-2">
                                <p class="text-sm text-gray-500">Published: ${book.published}</p>
                                <div id="button-container-${book.id}" class="flex flex-col gap-1"></div>
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
                            const deleteButtonElement = deleteBookButton(book, email);
                            if (deleteButtonElement) {
                                buttonContainer.appendChild(deleteButtonElement);
                            }
                        }
                    });
                    
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
                    // Save books globally for later use
                    window.existingBooks = books;
                })

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
                    const email = `<?php $user_email = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"], true)[0] : null;
                                    echo $user_email; ?>`
                    const username = `<?php $user_name = isset($_COOKIE["user_data"]) ? json_decode($_COOKIE["user_data"], true)[1] : null;
                                        echo $user_name; ?>`
                    // Render books
                    borrowedContainer.innerHTML = books.map(book =>
                        book.book_details.map(bookDetail => `
                <div class="relative w-full h-0 pb-[56.25%] bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="absolute inset-0 flex flex-col justify-between p-2 max-md:p-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">${bookDetail.name}</h4>
                            <p class="text-sm text-gray-600">Author: ${bookDetail.author}</p>
                            <p class="text-sm text-gray-600">Category: ${bookDetail.category}</p>
                             <p class="text-sm text-gray-600">Copies Left: ${bookDetail.quantity}</p>
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
                            onclick="deleteReservedBook('${username}', '${email}', ${bookDetail.id}, '${bookDetail.name}', '${bookDetail.src}')"
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
        showSpinner();
        $.ajax({
            type: "POST",
            url: "./backend/deleteBook.php",
            data: {
                id: id
            },
            success: function(data) {
                window.location.replace(`?page=books`);
                showSubpage("borrow", this)
            },
            error: function() {
                toastr.error("Failed to delete")
            },
            complete: function() {
                hideSpinner(); // Hide spinner after request completes
            }
        })
    }

    //delete reserved books
    function deleteReservedBook(username, email, id, name, src) {
        showSpinner();
        $.ajax({
            type: "POST",
            url: "./backend/deleteReservedBook.php",
            data: {
                username: username,
                email: email,
                id: id,
                book_name: name,
                src: src
            },
            dataType: "json", // Expect JSON response
            success: function(response) {
                if (response.status === "success") {
                    toastr.success(response.message || "Book successfully returned.");
                    fetchBooks(); // Refresh book list
                    fetchBorrowedBooks();
                } else {
                    toastr.error(response.message || "Failed to return book.");
                }
            },
            error: function(xhr) {
                toastr.error("Error: " + (xhr.responseJSON?.message || "Unexpected error occurred."));
                hideSpinner();
            },
            complete: function() {
                hideSpinner(); // Hide spinner after request completes
            }
        });
    }

    function reserveBook(book_id, bookname, user_email, src) {
        showSpinner();
        $.ajax({
            type: "POST",
            url: "./backend/reserveBooks.php",
            data: {
                user_email: user_email,
                book_id: book_id,
                bookname: bookname,
                src: src
            },
            dataType: "json", // Expect JSON response from backend
            success: function(response) {
                if (response.status === "success") {
                    toastr.success(response.message || "Book successfully reserved.");
                    fetchBooks();
                    fetchBorrowedBooks();
                } else {
                    toastr.error(response.message || "500!! Failed to reserve book.");
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Error: " + (xhr.responseJSON?.message || "Unexpected error occurred."));
                hideSpinner();
            },
            complete: function() {
                hideSpinner(); // Hide spinner after request completes
            }
        });
    }

    function showSpinner() {
        document.getElementById('spinner-overlay').style.display = 'flex';
    }

    function hideSpinner() {
        document.getElementById('spinner-overlay').style.display = 'none';
    }

    document?.getElementById('book-upload')?.addEventListener('change', function(event) {
        const fileInput = event.target;
        const formData = new FormData();
        formData.append('document', fileInput.files[0]);
        showSpinner();
        $.ajax({
            type: "POST",
            url: "./backend/bookUpload.php",
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content-type header
            success: function(data) {
                try {
                    if (data.success) {
                        uploadedBookUrl = data.BookUrl;
                        toastr.success("Book uploaded successfully.");
                    } else {
                        toastr.error(data.message || "Failed to upload Book.");
                    }
                } catch (error) {
                    toastr.error("Invalid response from server.");
                }
            },
            error: function() {
                toastr.error("404!! Client-side error.");
                hideSpinner(); // Hide spinner after request completes
            },
            complete: function() {
                hideSpinner(); // Hide spinner after request completes
            }
        });
    });

    document?.getElementById('book-upload-edit')?.addEventListener('change', function(event) {
        const fileInput = event.target;
        const formData = new FormData();
        formData.append('document', fileInput.files[0]);
        showSpinner();
        $.ajax({
            type: "POST",
            url: "./backend/bookUpload.php",
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content-type header
            success: function(data) {
                try {
                    if (data.success) {
                        uploadedBookUrl = data.BookUrl;
                        toastr.success("Book uploaded successfully.");
                    } else {
                        toastr.error(data.message || "Failed to upload Book.");
                    }
                } catch (error) {
                    toastr.error("Invalid response from server.");
                }
            },
            error: function() {
                toastr.error("404!! Client-side error.");
            },
            complete: function() {
                hideSpinner(); // Hide spinner after request completes
            }
        });
    });
</script>

<script>
    function updateFileName(input) {
        const fileLabel = document.getElementById('file-label');
        if (input.files && input.files[0]) {
            fileLabel.textContent = input.files[0].name;
        }
    }

    function editFileName(input) {
        const editLabel = document.getElementById('edit-label');
        if (input.files && input.files[0]) {
            editLabel.textContent = input.files[0].name;
        }
    }
</script>

<script>
    // Populate form fields when a book is selected
    document.getElementById('bookSelect')?.addEventListener('change', () => {
        const selectedBook = window.existingBooks.find(book => book.id === document.getElementById('bookSelect').value);
        console.log(selectedBook)
        if (selectedBook) {
            document.getElementById('name').value = selectedBook.name;
            document.getElementById('author').value = selectedBook.author;
            document.getElementById('category').value = selectedBook.category;
            document.getElementById('description').value = selectedBook.description;
            document.getElementById('published').value = selectedBook.published;
            document.getElementById('quantity').value = selectedBook.quantity;
            document.getElementById('rack_no').value = selectedBook.rack_no;
            document.getElementById('edit-label').textContent = selectedBook.src
        }
    });

    const populateEditForm = (book) => {
        document.getElementById("bookSelect").value = book.id;
        document.getElementById("name").value = book.name;
        document.getElementById("author").value = book.author;
        document.getElementById("category").value = book.category;
        document.getElementById("description").value = book.description;
        document.getElementById("published").value = book.published;
        document.getElementById("quantity").value = book.quantity;
        document.getElementById("rack_no").value = book.rack_no;
        document.getElementById("edit-label").textContent = book.src || "Choose a File";

        showSubpage('edit', this); // Show the edit form
    };
</script>