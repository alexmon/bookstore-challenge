<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Author;
use BookStoreAPI\BookStore\Application\Queries\ListAuthors\ListAuthorsQuery;
use BookStoreAPI\BookStore\Domain\Models\AuthorEntity;
use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\ListAuthorsViewModel;
use BookStoreAPI\SharedKernel\Domain\Bus\QueryBus\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class AuthorController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $author = new Author();
        $author->uuid = Uuid::uuid4()->toString();
        $author->name = $validated['name'];
        $author->save();

        return response()->json($author, 201);
    }

    public function index(): JsonResponse
    {
        /** @var AuthorEntity[] $authors */
        $authors = $this->queryBus->handle(new ListAuthorsQuery());

        $viewModel = new ListAuthorsViewModel($authors);

        return response()->json(['data' => $viewModel->render()], 200);
    }
}
