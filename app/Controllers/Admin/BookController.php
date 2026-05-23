<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\BookService;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->bookService = new BookService();
    }

    public function index(): void
    {
        $search = $this->request->input('search', '');
        $books = $this->bookService->searchCatalog($search);

        $msg = $_SESSION['msg'] ?? '';
        unset($_SESSION['msg']);

        $this->view('Admin.BookCatalog', [
            'books' => $books,
            'search' => $search,
            'msg' => $msg,
        ]);
    }

    public function create(): void
    {
        $genres = $this->bookService->getGenres();
        $this->view('Admin.BookForm', [
            'genres' => $genres,
            'book' => null,
            'errors' => [],
            'old_data' => [],
        ]);
    }

    public function store(): void
    {
        $data = $this->request->only([
            'title',
            'author',
            'isbn',
            'genre_id',
            'publisher',
            'published_year',
            'description',
        ]);

        $errors = [];
        if (!$this->validateBook($data, $errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('/admin/books/create');
            return;
        }

        $this->bookService->createBook($data);
        $_SESSION['msg'] = 'Book successfully added to the catalog.';
        $this->redirect('/admin/books');
    }

    public function edit(int $id): void
    {
        $book = $this->bookService->findBook($id);
        $genres = $this->bookService->getGenres();

        $this->view('Admin.BookForm', [
            'genres' => $genres,
            'book' => $book,
            'errors' => [],
            'old_data' => [],
        ]);
    }

    public function update(int $id): void
    {
        $data = $this->request->only([
            'title',
            'author',
            'isbn',
            'genre_id',
            'publisher',
            'published_year',
            'description',
        ]);

        $errors = [];
        if (!$this->validateBook($data, $errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_data'] = $data;
            $this->redirect('/admin/books/' . $id . '/edit');
            return;
        }

        $this->bookService->updateBook($id, $data);
        $_SESSION['msg'] = 'Book details updated successfully.';
        $this->redirect('/admin/books');
    }

    public function delete(int $id): void
    {
        $this->bookService->deleteBook($id);
        $_SESSION['msg'] = 'Book removed from catalog.';
        $this->redirect('/admin/books');
    }

    private function validateBook(array $data, array &$errors): bool
    {
        if (empty($data['title'])) {
            $errors['title'] = 'Book title is required';
        }
        if (empty($data['author'])) {
            $errors['author'] = 'Author name is required';
        }
        if (empty($data['isbn'])) {
            $errors['isbn'] = 'ISBN is required';
        }
        if (empty($data['genre_id'])) {
            $errors['genre_id'] = 'Please select a genre';
        }
        if (empty($data['published_year'])) {
            $errors['published_year'] = 'Publication year is required';
        }

        return empty($errors);
    }
}
