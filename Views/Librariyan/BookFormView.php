<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'librarian') {
    header('Location: ../LoginView.php');
    exit();
}

$mode = isset($_SESSION['book_form_mode']) ? $_SESSION['book_form_mode'] : 'add';
$book = isset($_SESSION['book_form_data']) ? $_SESSION['book_form_data'] : array();
$genres = isset($_SESSION['book_form_genres']) ? $_SESSION['book_form_genres'] : array();
$formTitle = $mode === 'edit' ? 'Edit Book' : 'Add New Book';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $formTitle; ?></title>
    <link rel="stylesheet" href="../css/librarian.css">
</head>

<body>

<h2><?php echo $formTitle; ?></h2>

<a href="/LibraryManagementSystem/Controllers/LibrarianBookCatalogController.php">Back to Catalog</a>

<?php if (isset($_SESSION['error']) && $_SESSION['error'] != '') { ?>
    <p><?php echo $_SESSION['error']; ?></p>
<?php } ?>

<hr>

<form novalidate action="/LibraryManagementSystem/Controllers/LibrarianBookSaveController.php" method="POST" enctype="multipart/form-data" onsubmit="return validateBookForm(this)">

    <input type="hidden" name="mode" value="<?php echo $mode; ?>">
    <input type="hidden" name="book_id" value="<?php echo isset($book['id']) ? $book['id'] : ''; ?>">
    <input type="hidden" name="current_cover_image_path" value="<?php echo isset($book['cover_image_path']) ? $book['cover_image_path'] : ''; ?>">

    <label for="title">Title:</label>
    <input type="text" name="title" id="title" value="<?php echo isset($book['title']) ? $book['title'] : ''; ?>">

    <br><br>

    <label for="author">Author:</label>
    <input type="text" name="author" id="author" value="<?php echo isset($book['author']) ? $book['author'] : ''; ?>">

    <br><br>

    <label for="isbn">ISBN:</label>
    <input type="text" name="isbn" id="isbn" value="<?php echo isset($book['isbn']) ? $book['isbn'] : ''; ?>">

    <br><br>

    <label for="genre_id">Genre:</label>
    <select name="genre_id" id="genre_id">
        <option value="">Select Genre</option>
        <?php foreach ($genres as $genre) { ?>
            <option value="<?php echo $genre['id']; ?>" <?php echo isset($book['genre_id']) && $book['genre_id'] == $genre['id'] ? 'selected' : ''; ?>>
                <?php echo $genre['name']; ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label for="publisher">Publisher:</label>
    <input type="text" name="publisher" id="publisher" value="<?php echo isset($book['publisher']) ? $book['publisher'] : ''; ?>">

    <br><br>

    <label for="published_year">Published Year:</label>
    <input type="text" name="published_year" id="published_year" value="<?php echo isset($book['published_year']) ? $book['published_year'] : ''; ?>">

    <br><br>

    <label for="description">Description:</label>
    <br>
    <textarea name="description" id="description" rows="5" cols="50"><?php echo isset($book['description']) ? $book['description'] : ''; ?></textarea>

    <br><br>

    <label for="cover_image">Cover Image:</label>
    <input type="file" name="cover_image" id="cover_image">

    <br><br>

    <?php
        // Show quantity input for both add and edit (pre-fill on edit)
        $quantityVal = '';
        if ($mode === 'edit') {
            $quantityVal = isset($book['quantity']) ? intval($book['quantity']) : '';
        } else {
            $quantityVal = 1;
        }
    ?>
    <label for="quantity">Quantity (copies) for your branch:</label>
    <input type="number" name="quantity" id="quantity" min="1" value="<?php echo $quantityVal; ?>">
    <br><br>

    <br><br>

    <?php if (isset($book['cover_image_path']) && $book['cover_image_path'] != '') { ?>
        <p>Current Cover:</p>
        <img src="../../<?php echo $book['cover_image_path']; ?>" width="120" alt="Current Cover">
    <?php } ?>

    <br><br>

    <button type="submit"><?php echo $mode === 'edit' ? 'Update Book' : 'Save Book'; ?></button>

</form>

<script src="../js/admin_validation.js"></script>
</body>
</html>
