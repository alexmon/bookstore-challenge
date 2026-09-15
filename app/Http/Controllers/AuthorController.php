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
use Illuminate\Support\Facades\Log;

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
            Log::error('Create author validation error', [
                'exception' => $e,
            ]);

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
