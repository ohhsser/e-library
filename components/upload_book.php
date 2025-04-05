<div id="upload" class="subpage hidden">
    <h2 class="text-3xl font-bold mb-4">Upload Book</h2>
    <!-- Upload Book Form -->
    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="text" name="name" placeholder="Book Name" required class="w-full p-3 border rounded">
        <input type="text" name="author" placeholder="Author" required class="w-full p-3 border rounded">
        <input type="text" name="category" placeholder="Category" required class="w-full p-3 border rounded">
        <textarea name="description" placeholder="Description" required
            class="w-full p-3 border rounded min-h-32"></textarea>
        <input type="date" name="published" placeholder="Published (e.g., 2025)" required
            class="w-full p-3 border rounded">
        <div class="w-full">
            <label for="book-upload" id="file-label" class="block w-full p-3 border rounded cursor-pointer text-left bg-white hover:bg-gray-100">
                Choose a File
            </label>
            <input type="file" id="book-upload" name="src" required class="hidden" onchange="updateFileName(this)">
        </div>

        <input type="number" name="quantity" placeholder="Quantity" required class="w-full p-3 border rounded">
        <input type="text" name="rack_no" placeholder="Rack No" required class="w-full p-3 border rounded">
        <button type="submit" name="upload" id="upload" class="w-full bg-black text-white p-3 rounded">Upload
            Book</button>
    </form>
</div>