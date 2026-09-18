<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use BookStoreAPI\BookStore\Application\Commands\BorrowBook\BorrowBookCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateBook\CreateBookCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateLoanEntry\CreateLoanEntryCommand;
use BookStoreAPI\BookStore\Application\Commands\FetchAndLockBook\FetchAndLockBookCommand;
use BookStoreAPI\BookStore\Application\Commands\ReturnBook\ReturnBookCommand;
use BookStoreAPI\BookStore\Application\Queries\FetchAuthor\FetchAuthorQuery;
use BookStoreAPI\BookStore\Application\Queries\FetchBook\FetchBookQuery;
use BookStoreAPI\BookStore\Application\Queries\SearchBooks\SearchBooksQuery;
use BookStoreAPI\BookStore\Domain\Exceptions\AuthorNotFoundException;
use BookStoreAPI\BookStore\Domain\Exceptions\BookAlreadyExists;
use BookStoreAPI\BookStore\Domain\Exceptions\BorrowException;
use BookStoreAPI\BookStore\Domain\Exceptions\ErrorCode;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidAuthorIdException;
use BookStoreAPI\BookStore\Domain\Exceptions\InvalidBookIdException;
use BookStoreAPI\BookStore\Domain\Exceptions\ReturnException;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Domain\Models\BookEntity;
use BookStoreAPI\BookStore\Domain\Models\BorrowerEntity;
use BookStoreAPI\BookStore\Domain\Models\LoanAction;
use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\BookBorrowerViewModel;
use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\CreateBookViewModel;
use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\FetchBookViewModel;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\CommandBus;
use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\QueryBus;
use BookStoreAPI\SharedKernel\Domain\Database\DatabaseTransaction;
use BookStoreAPI\SharedKernel\Domain\Exceptions\InvalidISBNException;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Http\Response\ErrorMessages;
use BookStoreApi\SharedKernel\Infrastructure\Service\ExceptionHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BookController extends Controller
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private DatabaseTransaction $databaseTransaction,
    ) {}

    /**
     * Responses
     * - 201 Created
     * - 400 Bad Request
     * - 409 Conflict
     * - 422 Unprocessable Entity
     *
     * @throws ValidationException|UnprocessableEntityHttpException|ConflictHttpException
     */
    public function store(Request $request): JsonResponse
    {
        $title = $request->input('title', null);
        $isbn = $request->input('isbn', null);
        $authorUuid = $request->input('author_uuid', null);

        $createBookCommand = new CreateBookCommand(
            title: $title,
            isbn: $isbn,
            authorUuid: $authorUuid,
        );

        try {
            $this->commandBus->handle($createBookCommand);
        } catch (ValidationException $e) {
            Log::error(
                '[Create book] validation error',
                [
                    'exception' => $e,
                    'errors' => $e->getErrors(),
                ]
            );

            throw $e;
        } catch (InvalidAuthorIdException $e) {
            Log::error('[Create book] Invalid author ID', ['exception' => ExceptionHelper::getFileLineAsString($e), 'authorUuid' => $authorUuid]);

            throw new UnprocessableEntityHttpException(ErrorMessages::getMessageByErrorCode($e->getCode()), $e);
        } catch (AuthorNotFoundException $e) {
            Log::error('[Create book] Author not found', ['exception' => ExceptionHelper::getFileLineAsString($e), 'authorUuid' => $authorUuid]);

            throw new UnprocessableEntityHttpException(ErrorMessages::getMessageByErrorCode($e->getCode()), $e);
        } catch (InvalidISBNException $e) {
            Log::error('[Create book] Invalid ISBN', ['exception' => ExceptionHelper::getFileLineAsString($e), 'isbn' => $isbn]);

            throw new UnprocessableEntityHttpException(ErrorMessages::getMessageByErrorCode($e->getCode()), $e);
        } catch (BookAlreadyExists $e) {
            Log::error('[Create book] Book already exists', ['exception' => ExceptionHelper::getFileLineAsString($e), 'title' => $title, 'isbn' => $isbn]);

            throw new ConflictHttpException(ErrorMessages::getMessageByErrorCode($e->getCode()), $e);
        }

        /** @var array{book: BookEntity, author: AuthorEntity} $result */
        $result = $createBookCommand->getResult();
        $book = $result['book'];
        $author = $result['author'];

        $viewModel = new CreateBookViewModel(
            book: $book,
            author: $author,
        );

        Log::info('[Create book] success', ['title' => $title, 'isbn' => $isbn, 'authorUuid' => $authorUuid]);

        return response()->json($viewModel->render(), 201);
    }

    /**
     * Responses:
     * - 200 OK
     *
     * Support search books by title, author UUID and availability.
     *
     * Assumption:  using limit / offset query - usage on a authorized request with paginator
     * could also be used a cursor search
     */
    public function index(Request $request): JsonResponse
    {
        /** @var ?string $search */
        $search = $request->query('search', null);
        /** @var ?string $authorUuid */
        $authorUuid = $request->query('author', null);
        // available filter takes values [null, 0, 1]
        $available = boolval($request->query('available', null));
        $page = intval($request->query('page', '1'));
        $perPage = intval($request->query('per_page', env('LISTING_PER_PAGE', '10')));

        Log::info('[Search books] Request', ['search' => $search, 'authorUuid' => $authorUuid, 'available' => $available, 'page' => $page, 'perPage' => $perPage]);

        $booksResults = $this->queryBus->handle(new SearchBooksQuery(
            search: $search,
            page: $page,
            perPage: $perPage,
            authorUuid: $authorUuid,
            available: $available,
        ));

        return response()->json([
            'current_page' => $booksResults['current_page'],
            'per_page' => $booksResults['per_page'],
            'total' => $booksResults['total'],
            'last_page' => $booksResults['last_page'],
            'has_more_pages' => $booksResults['has_more_pages'],
            // if item is array it is already in the desired format retrived from cache
            'items' => \array_map(
                fn($book) => \is_array($book) ? $book : (new FetchBookViewModel($book))->render(),
                $booksResults['items']
            ),
            'total_pages' => $booksResults['total_pages'],
        ], 200);
    }

    /**
     * Responses:
     * - 200 OK
     * - 400 Bad Request
     * - 404 Not Found
     * - 422 Unprocessable Entity
     *
     * @throws ValidationException|UnprocessableEntityHttpException|NotFoundHttpException|ConflictHttpException
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            /** @var BookEntity|null $book */
            $book = $this->queryBus->handle(new FetchBookQuery($uuid));
        } catch (InvalidBookIdException $e) {
            Log::error('[Fetch book] Invalid book ID', ['exception' => ExceptionHelper::getFileLineAsString($e), 'bookId' => $uuid]);

            throw new UnprocessableEntityHttpException(ErrorMessages::getMessageByErrorCode($e->getCode()), $e);
        } catch (ValidationException $e) {
            Log::error('[Fetch book] Validation error', ['exception' => ExceptionHelper::getFileLineAsString($e), 'bookId' => $uuid, 'errors' => $e->getErrors()]);

            throw $e;
        }

        if (null === $book) {
            Log::error('[Fetch book] Book not found', ['bookId' => $uuid]);

            throw new NotFoundHttpException(ErrorMessages::getMessageByErrorCode(ErrorCode::BOOK_NOT_FOUND->value));
        }

        if (null === $book->getAuthor()) {
            Log::warning('Author not found for book', ['book_uuid' => $book->getId()->getValue()]);
        }

        $viewModel = new FetchBookViewModel($book);

        return response()->json($viewModel->render(), 200);
    }

    /**
     * Responses:
     * - 201 Created
     * - 400 Bad Request
     * - 404 Not Found
     * - 422 Unprocessable Entity
     * @throws ValidationException|UnprocessableEntityHttpException|NotFoundHttpException|ConflictHttpException
     */
    public function borrow(string $uuid, Request $request): JsonResponse
    {
        $borrowerName = $request->input('borrower', null);
        $book = null;

        $this->databaseTransaction->beginTransaction();
        Log::info('[Borrow book] transaction started', ['bookId' => $uuid, 'borrower' => $borrowerName]);

        try {
            $book = $this->transactionalBorrow($uuid, $borrowerName);
            $this->databaseTransaction->commitTransaction();
            Log::info('[Borrow book] transaction committed', ['bookId' => $uuid, 'borrower' => $borrowerName]);

        } catch (\Exception $e) {
            $this->databaseTransaction->rollBackTransaction();
            Log::error('[Borrow book] transaction rollback', ['exception' => ExceptionHelper::getFileLineAsString($e), 'bookId' => $uuid, 'borrower' => $borrowerName]);

            throw $e;
        }

        $viewModel = new BookBorrowerViewModel($book);

        return response()->json($viewModel->render(), 200);
    }

    private function transactionalBorrow(string $bookUuid, ?string $borrowerName): BookEntity
    {
        $book = null;
        try {
            /**
             * We use this command to ensure our SQL query hits master replica
             * Since all commands should be directed to the master replica, this ensures we get the most up-to-date data.
             */
            $fetchAndLockBookCommand = new FetchAndLockBookCommand($bookUuid);
            $this->commandBus->handle($fetchAndLockBookCommand);
            /** @var BookEntity|null $book */
            $book = $fetchAndLockBookCommand->getResult();
        } catch (ValidationException $e) {
            Log::error('[Borrow book] Fetch book validation error', ['exception' => ExceptionHelper::getFileLineAsString($e), 'errors' => $e->getErrors()]);

            throw $e;
        }

        if (null === $book) {
            Log::error('[Borrow book] Book not found', ['bookId' => $bookUuid]);

            throw new NotFoundHttpException(ErrorMessages::getMessageByErrorCode(ErrorCode::BOOK_NOT_FOUND->value));
        }

        $borrower = null;
        try {
            $findOrCreateBorrowerCommand = new FindOrCreateBorrowerCommand(
                $borrowerName
            );
            $this->commandBus->handle($findOrCreateBorrowerCommand);
            /** @var BorrowerEntity $borrower */
            $borrower = $findOrCreateBorrowerCommand->getResult();
        } catch (ValidationException $e) {
            Log::error('[Borrow book] Create borrower validation error', ['exception' => ExceptionHelper::getFileLineAsString($e), 'errors' => $e->getErrors()]);

            throw $e;
        }

        try {
            $borrowBookCommand = new BorrowBookCommand(
                $book,
                $borrower,
            );
            $this->commandBus->handle($borrowBookCommand);
        } catch (BorrowException $e) {
            Log::error('[Borrow book] Borrow exception', ['exception' => ExceptionHelper::getFileLineAsString($e), 'bookId' => $bookUuid]);

            match ($e->getCode()) {
                ErrorCode::BOOK_BORROW_REQUEST_ON_INACTIVE->value => throw new ConflictHttpException(ErrorMessages::getMessageByErrorCode(ErrorCode::BOOK_BORROW_REQUEST_ON_INACTIVE->value), $e),
                ErrorCode::BOOK_BORROW_REQUEST_ON_UNAVAILABLE->value => throw new ConflictHttpException(ErrorMessages::getMessageByErrorCode(ErrorCode::BOOK_BORROW_REQUEST_ON_UNAVAILABLE->value), $e),
                default => throw new ConflictHttpException('Conflict resource', $e),
            };
        }

        $createLoanEntryCommand = new CreateLoanEntryCommand(
            book: $book,
            borrower: $borrower,
            action: LoanAction::BORROW,
        );
        $this->commandBus->handle($createLoanEntryCommand);
        Log::info('[Borrow book] created loan entry', ['bookId' => $bookUuid]);

        return $book;
    }

    /**
     * Responses:
     * - 201 Created
     * - 400 Bad Request
     * - 404 Not Found
     * - 422 Unprocessable Entity
     * - 409 Conflict
     *
     * @throws ValidationException|UnprocessableEntityHttpException|NotFoundHttpException|ConflictHttpException
     */
    public function returnBook(string $uuid, Request $request): JsonResponse
    {
        $book = null;
        $this->databaseTransaction->beginTransaction();
        Log::info('[Return book] Starting return transaction', ['bookId' => $uuid]);

        try {
            $book = $this->transactionalReturnBook($uuid, null);
            $this->databaseTransaction->commitTransaction();
            Log::info('[Return book] Return transaction committed', ['bookId' => $uuid]);
        } catch (\Exception $e) {
            $this->databaseTransaction->rollBackTransaction();
            Log::error('[Return book] Return transaction rollback', ['exception' => ExceptionHelper::getFileLineAsString($e), 'bookId' => $uuid]);

            throw $e;
        }

        $viewModel = new BookBorrowerViewModel($book);
        return response()->json($viewModel->render(), 200);
    }

    /**
     * TODO nice to have a check that the request to return is associated with the active borrower
     */
    private function transactionalReturnBook(string $bookUuid, ?string $borrowerId): BookEntity
    {
        $book = null;
        $borrower = null;
        try {
            /**
             * We use this command to ensure our SQL query hits master replica
             * Since all commands should be directed to the master replica, this ensures we get the most up-to-date data.
             */
            $fetchAndLockBookCommand = new FetchAndLockBookCommand($bookUuid);
            $this->commandBus->handle($fetchAndLockBookCommand);
            /** @var BookEntity|null $book */
            $book = $fetchAndLockBookCommand->getResult();
        } catch (ValidationException $e) {
            Log::error('[Return book] Fetch book validation error', ['exception' => ExceptionHelper::getFileLineAsString($e), 'errors' => $e->getErrors()]);

            throw $e;
        }

        if (null === $book) {
            Log::error('[Return book] Book not found', ['bookId' => $bookUuid]);
            throw new NotFoundHttpException(ErrorMessages::getMessageByErrorCode(ErrorCode::BOOK_NOT_FOUND->value));
        }

        $borrower = $book->getBorrower();
        if (null === $borrower || $book->isAvailable()) {
            Log::error('[Return book] Borrower not found or book is already available', ['bookId' => $bookUuid]);
            throw new ConflictHttpException(ErrorMessages::getMessageByErrorCode(ErrorCode::BOOK_RETURN_REQUEST_ON_AVAILABLE_BOOK->value));
        }

        try {
            $returnBookCommand = new ReturnBookCommand(
                book: $book,
                borrower: $borrower,
            );
            $this->commandBus->handle($returnBookCommand);
        } catch (ReturnException $e) {
            Log::error('[Return book] Return book error', ['exception' => ExceptionHelper::getFileLineAsString($e), 'bookId' => $bookUuid]);

            throw new ConflictHttpException(ErrorMessages::getMessageByErrorCode($e->getCode()), $e);
        }

        $createLoanEntryCommand = new CreateLoanEntryCommand(
            book: $book,
            borrower: $borrower,
            action: LoanAction::RETURN,
        );
        $this->commandBus->handle($createLoanEntryCommand);
        Log::info('[Return book] created loan entry', ['bookId' => $bookUuid]);

        return $book;
    }
}
