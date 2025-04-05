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
            class="w-full p-3 border rounded min-h-32"></textarea>
        <input type="text" name="published" id="published" placeholder="Published (e.g., 2025)"
            class="w-full p-3 border rounded">
        <div class="w-full">
            <label for="book-upload" id="file-label" class="block w-full p-3 border rounded cursor-pointer text-left bg-white hover:bg-gray-100">
                Choose a File
            </label>
            <input type="file" id="book-upload" name="src" required class="hidden" onchange="updateFileName(this)">
        </div>
        <input type="number" name="quantity" id="quantity" placeholder="Quantity" class="w-full p-3 border rounded">
        <input type="text" name="rack_no" id="rack_no" placeholder="Rack No" class="w-full p-3 border rounded">
        <button type="submit" name="edit" id="edit" class="w-full bg-black text-white p-3 rounded">Edit
            Book</button>
    </form>
</div>
<script>
    // Populate form fields when a book is selected
    document.getElementById('bookSelect').addEventListener('change', () => {
        const selectedBook = window.existingBooks.find(book => book.id === document.getElementById('bookSelect').value);
        if (selectedBook) {
            document.getElementById('name').value = selectedBook.name;
            document.getElementById('author').value = selectedBook.author;
            document.getElementById('category').value = selectedBook.category;
            document.getElementById('description').value = selectedBook.description;
            document.getElementById('published').value = selectedBook.published;
            document.getElementById('quantity').value = selectedBook.quantity;
            document.getElementById('rack_no').value = selectedBook.rack_no;
        }
    });
</script>