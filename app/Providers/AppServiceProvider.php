<?php

declare(strict_types=1);

namespace App\Providers;

use BookStoreAPI\BookStore\Application\Commands\CreateAuthor\CreateAuthorCommand;
use BookStoreAPI\BookStore\Application\Commands\CreateAuthor\CreateAuthorCommandHandler;
use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQuery;
use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQueryHandler;
use BookStoreAPI\BookStore\Domain\Models\AuthorRepository;
use BookStoreAPI\BookStore\Domain\Models\BookRepository;
use BookStoreAPI\BookStore\Infrastructure\Repositories\WrappedEloquentAuthorRepository;
use BookStoreAPI\BookStore\Infrastructure\Repositories\WrappedEloquentBookRepository;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\CommandBus;
use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\QueryBus;
use BookStoreAPI\SharedKernel\Infrastructure\Bus\CommandBus\TacticianCommandBus as WrappedTacticianCommandBus;
use BookStoreAPI\SharedKernel\Infrastructure\Bus\QueryBus\TacticianQueryBus;
use BookStoreAPI\SharedKernel\Infrastructure\Service\ValidationService;
use Illuminate\Support\ServiceProvider;
use League\Tactician\CommandBus as TacticianCommandBus;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
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
                            ]
                        ),
                        new HandleInflector()
                    ),
                ])
            );
        });

        // WrappedEloquentAuthorRepository
        $this->app->singleton(AuthorRepository::class, function ($app) {
            return new WrappedEloquentAuthorRepository();
        });

        // WrappedEloquentBookRepository
        $this->app->singleton(BookRepository::class, function ($app) {
            return new WrappedEloquentBookRepository();
        });

        $this->app->singleton(ValidatorInterface::class, function ($app) {
            return Validation::createValidatorBuilder()
                ->setConstraintValidatorFactory(new ConstraintValidatorFactory())
                ->enableAttributeMapping()
                ->getValidator();
        });

        // Validation Service
        $this->app->singleton(ValidationService::class, function ($app) {
            return new ValidationService($app->make(ValidatorInterface::class));
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
