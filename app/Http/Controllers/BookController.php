<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use BookStoreAPI\BookStore\Application\Commands\CreateBook\CreateBookCommand;
use BookStoreAPI\BookStore\Application\Queries\FetchAuthor\FetchAuthorQuery;
use BookStoreAPI\BookStore\Application\Queries\FetchBook\FetchBookQuery;
use BookStoreAPI\BookStore\Domain\Exceptions\AuthorNotFoundException;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidAuthorIdException;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidBookIdException;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\CreateBookViewModel;
use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\FetchBookViewModel;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\CommandBus;
use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\QueryBus;
use BookStoreAPI\SharedKernel\Domain\Exceptions\InvalidISBNException;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BookController extends Controller
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {}

    /**
     * @throws ValidationException|UnprocessableEntityHttpException
     */
    public function store(Request $request): JsonResponse
    {
        $createBookCommand = new CreateBookCommand(
            title: $request->input('title', null),
            isbn: $request->input('isbn', null),
            authorUuid: $request->input('author_uuid', null),
        );

        try {
            $this->commandBus->handle($createBookCommand);
        } catch (ValidationException $e) {
            Log::error('Create book validation error', ['exception' => $e]);

            throw $e;
        } catch (InvalidAuthorIdException $e) {
            Log::error('Invalid author ID', ['exception' => $e]);

            throw new UnprocessableEntityHttpException('Invalid author ID.', $e);
        } catch (AuthorNotFoundException $e) {
            Log::error('Author not found', ['exception' => $e]);

            throw new UnprocessableEntityHttpException('Author not found.', $e);
        } catch (InvalidISBNException $e) {
            Log::error('Invalid ISBN', ['exception' => $e]);

            throw new UnprocessableEntityHttpException('Invalid ISBN.', $e);
        }

        /** @var array{book: BookEntity, author: AuthorEntity} $result */
        $result = $createBookCommand->getResult();
        $book = $result['book'];
        $author = $result['author'];

        $viewModel = new CreateBookViewModel(
            book: $book,
            author: $author,
        );

        return response()->json($viewModel->render(), 201);
    }

    /**
     * TODO: add caching and pagination
     */
    public function index(Request $request): JsonResponse
    {
        // NOTE: eager loading we are OK
        $query = Book::with('author');

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($request->has('author')) {
            $authorUuid = $request->query('author');
            $author = Author::where('uuid', $authorUuid)->first();
            if ($author) {
                $query->where('author_id', $author->id);
            } else {
                return response()->json(['data' => []], 200);
            }
        }

        $books = $query->get();

        return response()->json(['data' => $books], 200);
    }

    /**
     * @throws ValidationException|UnprocessableEntityHttpException|NotFoundHttpException
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            /** @var BookEntity|null $book */
            $book = $this->queryBus->handle(new FetchBookQuery($uuid));
        } catch (InvalidBookIdException $e) {
            Log::error('Invalid book ID', ['exception' => $e]);

            throw new UnprocessableEntityHttpException('Invalid book ID.', $e);
        } catch (ValidationException $e) {
            Log::error('Validation error', ['exception' => $e]);

            throw $e;
        }

        if (null === $book) {
            throw new NotFoundHttpException('Book not found.');
        }

        try {
            // TODO: should allow pass AuthorId as argument type?
            /** @var AuthorEntity|null $author */
            $author = $this->queryBus->handle(new FetchAuthorQuery($book->getAuthor()->getValue()));
        } catch (InvalidAuthorIdException $e) {
            Log::error('Invalid author ID', ['exception' => $e]);

            throw new UnprocessableEntityHttpException('Invalid author ID.', $e);
        } catch (ValidationException $e) {
            Log::error('Validation error', ['exception' => $e]);

            throw $e;
        }

        if (null === $author) {
            Log::warning('Author not found for book', ['book_uuid' => $book->getId()->getValue()]);
        }

        $viewModel = new FetchBookViewModel(
            book: $book,
            author: $author,
        );

        return response()->json($viewModel->render(), 200);
    }
}
