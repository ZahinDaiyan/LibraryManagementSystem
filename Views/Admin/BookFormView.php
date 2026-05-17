<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../LoginView.php');
    exit();
}

$book = $_SESSION['edit_book'] ?? null;
$genres = $_SESSION['genres'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];

unset($_SESSION['form_errors'], $_SESSION['old_data']);

$title = $book ? "Edit Book Details" : "Add New Book to Master Catalog";
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
</head>
<body>

<h2><?= $title ?></h2>
<a href="BookCatalogView.php">← Back to Catalog</a>
<hr>

<form novalidate action="../../Controllers/AdminBookActionController.php" method="POST" onsubmit="return validateBookForm(this)">
    <input type="hidden" name="action" value="<?= $book ? 'update' : 'create' ?>">
    <?php if ($book): ?>
        <input type="hidden" name="id" value="<?= $book['id'] ?>">
    <?php endif; ?>

    <p>
        <label>Book Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($old_data['title'] ?? $book['title'] ?? '') ?>" style="width: 300px;">
        <?php if (isset($errors['title'])): ?>
            <span style="color:red;"><br><?= $errors['title'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Author Name:</label><br>
        <input type="text" name="author" value="<?= htmlspecialchars($old_data['author'] ?? $book['author'] ?? '') ?>" style="width: 300px;">
        <?php if (isset($errors['author'])): ?>
            <span style="color:red;"><br><?= $errors['author'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>ISBN:</label><br>
        <input type="text" name="isbn" value="<?= htmlspecialchars($old_data['isbn'] ?? $book['isbn'] ?? '') ?>">
        <?php if (isset($errors['isbn'])): ?>
            <span style="color:red;"><br><?= $errors['isbn'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Genre:</label><br>
        <select name="genre_id">
            <option value="">Select Genre</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?= $g['id'] ?>" <?= ($old_data['genre_id'] ?? $book['genre_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                    <?= $g['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['genre_id'])): ?>
            <span style="color:red;"><br><?= $errors['genre_id'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Publisher:</label><br>
        <input type="text" name="publisher" value="<?= htmlspecialchars($old_data['publisher'] ?? $book['publisher'] ?? '') ?>">
        <?php if (isset($errors['publisher'])): ?>
            <span style="color:red;"><br><?= $errors['publisher'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Published Year:</label><br>
        <input type="number" name="published_year" value="<?= $old_data['published_year'] ?? $book['published_year'] ?? '' ?>" min="1800" max="<?= date('Y') ?>">
        <?php if (isset($errors['published_year'])): ?>
            <span style="color:red;"><br><?= $errors['published_year'] ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label>Description:</label><br>
        <textarea name="description" rows="5" cols="40"><?= htmlspecialchars($old_data['description'] ?? $book['description'] ?? '') ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <span style="color:red;"><br><?= $errors['description'] ?></span>
        <?php endif; ?>
    </p>

    <button type="submit"><?= $book ? 'Update Book' : 'Add to Catalog' ?></button>
</form>

<script src="../js/admin_validation.js"></script>
</body>
</html>

