<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use BookStoreAPI\BookStore\Application\Commands\CreateAuthor\CreateAuthorCommand;
use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQuery;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\CreateAuthorViewModel;
use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\ListAuthorsViewModel;
use BookStoreAPI\SharedKernel\Domain\Bus\CommandBus\CommandBus;
use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\QueryBus;
use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $name = $request->input('name', null);

        $command = new CreateAuthorCommand(
            name: $name,
        );

        try {
            $this->commandBus->handle($command);
        } catch (ValidationException $e) {
            // TODO: log the validation exception

            throw $e;
        }

        /** @var AuthorEntity $author */
        $author = $command->getResult();
        $viewModel = new CreateAuthorViewModel($author);

        return response()->json($viewModel->render(), 201);
    }

    public function index(): JsonResponse
    {
        /** @var AuthorEntity[] $authors */
        $authors = $this->queryBus->handle(new ListAuthorsQuery());

        $viewModel = new ListAuthorsViewModel($authors);

        return response()->json(['data' => $viewModel->render()], 200);
    }
}
