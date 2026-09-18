<?php

declare(strict_types=1);

namespace App\Providers;

use BookStoreAPI\BookStore\Application\Commands\BorrowBook\BorrowBookCommand;
use BookStoreAPI\BookStore\Application\Commands\BorrowBook\BorrowBookCommandHandler;
use BookStoreAPI\BookStore\Application\Commands\CreateAuthor\CreateAuthorCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateAuthor\CreateAuthorCommandHandler;
use BookStoreAPI\BookStore\Application\Commands\CreateBook\CreateBookCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateBook\CreateBookCommandHandler;
use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateBorrower\FindOrCreateBorrowerCommandHandler;
use BookStoreAPI\BookStore\Application\Commands\CreateLoanEntry\CreateLoanEntryCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateLoanEntry\CreateLoanEntryCommandHandler;
use BookStoreAPI\BookStore\Application\Commands\FetchAndLockBook\FetchAndLockBookCommand;
use BookStoreAPI\BookStore\Application\Commands\FetchAndLockBook\FetchAndLockBookCommandHandler;
use BookStoreAPI\BookStore\Application\Commands\ReturnBook\ReturnBookCommand;
use BookStoreAPI\BookStore\Application\Commands\ReturnBook\ReturnBookCommandHandler;
use BookStoreAPI\BookStore\Application\Queries\FetchAuthor\FetchAuthorQuery;
use BookStoreAPI\BookStore\Application\Queries\FetchAuthor\FetchAuthorQueryHandler;
use BookStoreAPI\BookStore\Application\Queries\FetchBook\FetchBookQuery;
use BookStoreAPI\BookStore\Application\Queries\FetchBook\FetchBookQueryHandler;
use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQuery;
use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQueryHandler;
use BookStoreAPI\BookStore\Application\Queries\SearchBooks\SearchBooksQuery;
use BookStoreAPI\BookStore\Application\Queries\SearchBooks\SearchBooksQueryHandler;
use BookStoreAPI\BookStore\Domain\Cache\BookSearchResultsCacheService;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\BookStore\Domain\Models\BorrowerRepository;
use BookStoreAPI\BookStore\Domain\Models\LoanRepository;
use BookStoreAPI\BookStore\Infrastructure\Cache\ConcreteBookSearchResultsCacheService;
use BookStoreAPI\BookStore\Infrastructure\Repositories\EloquentAdapterAuthorRepository;
use BookStoreAPI\BookStore\Infrastructure\Repositories\EloquentAdapterBookRepository;
use BookStoreAPI\BookStore\Infrastructure\Repositories\EloquentAdapterBorrowerRepository;
use BookStoreAPI\BookStore\Infrastructure\Repositories\EloquentAdapterLoanRepository;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\CommandBus;
use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\QueryBus;
use BookStoreAPI\SharedKernel\Domain\Database\DatabaseTransaction;
use BookStoreAPI\SharedKernel\Infrastructure\Bus\CommandBus\TacticianCommandBus as WrappedTacticianCommandBus;
use BookStoreAPI\SharedKernel\Infrastructure\Bus\QueryBus\TacticianQueryBus;
use BookStoreAPI\SharedKernel\Infrastructure\Cache\LaravelRedisAdapter;
use BookStoreAPI\SharedKernel\Infrastructure\Service\EloquentDatabaseTransactionAdapterService;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use Illuminate\Support\ServiceProvider;
use League\Tactician\CommandBus as TacticianCommandBus;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Command Bus
        $this->app->singleton(CommandBus::class, function ($app) {
            return new WrappedTacticianCommandBus(
                new TacticianCommandBus([
                    new CommandHandlerMiddleware(
                        new ClassNameExtractor(),
                        new ContainerLocator(
                            $app,
                            [
                                // Mapping of command classes to their respective handlers goes here.
                                // TODO: apply NamingLocator for automatic handler resolution.
                                CreateAuthorCommand::class => CreateAuthorCommandHandler::class,
                                CreateBookCommand::class => CreateBookCommandHandler::class,
                                FetchAndLockBookCommand::class => FetchAndLockBookCommandHandler::class,
                                FindOrCreateBorrowerCommand::class => FindOrCreateBorrowerCommandHandler::class,
                                BorrowBookCommand::class => BorrowBookCommandHandler::class,
                                CreateLoanEntryCommand::class => CreateLoanEntryCommandHandler::class,
                                ReturnBookCommand::class => ReturnBookCommandHandler::class,
                            ]
                        ),
                        new HandleInflector()
                    )
                ])
            );
        });

        // Query Bus
        $this->app->singleton(QueryBus::class, function ($app) {
            return new TacticianQueryBus(
                new TacticianCommandBus([
                    new CommandHandlerMiddleware(
                        new ClassNameExtractor(),
                        new ContainerLocator(
                            $app,
                            [
                                // Mapping of query classes to their respective handlers goes here.
                                // TODO: apply NamingLocator for automatic handler resolution.
                                ListAuthorsQuery::class => ListAuthorsQueryHandler::class,
                                FetchBookQuery::class => FetchBookQueryHandler::class,
                                FetchAuthorQuery::class => FetchAuthorQueryHandler::class,
                                SearchBooksQuery::class => SearchBooksQueryHandler::class,
                            ]
                        ),
                        new HandleInflector()
                    ),
                ])
            );
        });

        // TODO use Abstract Factory for Eloquent classes of repositories / transaction

        // Repositories

        $this->app->singleton(AuthorRepository::class, function ($app) {
            return new EloquentAdapterAuthorRepository();
        });

        $this->app->singleton(BookRepository::class, function ($app) {
            return new EloquentAdapterBookRepository();
        });

        $this->app->singleton(BorrowerRepository::class, function ($app) {
            return new EloquentAdapterBorrowerRepository();
        });

        $this->app->singleton(LoanRepository::class, function ($app) {
            return new EloquentAdapterLoanRepository();
        });


        // Database

        $this->app->singleton(DatabaseTransaction::class, function ($app) {
            return new EloquentDatabaseTransactionAdapterService();
        });

        // Validation

        $this->app->singleton(ValidatorInterface::class, function ($app) {
            return Validation::createValidatorBuilder()
                ->setConstraintValidatorFactory(new ConstraintValidatorFactory())
                ->enableAttributeMapping()
                ->getValidator();
        });

        $this->app->singleton(ValidationService::class, function ($app) {
            return new ValidationService($app->make(ValidatorInterface::class));
        });

        // CacheInterface
        $this->app->singleton(CacheInterface::class, function ($app) {
            return new LaravelRedisAdapter();
        });

        // BookSearchResultsCacheService
        $this->app->singleton(BookSearchResultsCacheService::class, function ($app) {
            return new ConcreteBookSearchResultsCacheService($app->make(CacheInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
