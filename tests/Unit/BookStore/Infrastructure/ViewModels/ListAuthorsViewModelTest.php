<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Infrastructure\ViewModels;

use BookStoreAPI\BookStore\Infrastructure\Http\ViewModels\ListAuthorsViewModel;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\AuthorEntityFixture;

class ListAuthorsViewModelTest extends TestCase
{
    public function testListAuthorsViewModel(): void
    {
        $author = AuthorEntityFixture::create();
        $viewModel = new ListAuthorsViewModel([$author]);

        /**
         * @var array<int, {
         *     'uuid': string,
         *     'name': string,
         *     'created_at': string,
         *     'updated_at': string,
         * }> $authors
         */
        $authors = $viewModel->render();

        $this->assertCount(1, $authors);
        $this->assertIsArray($authors[0]);
        $this->assertArrayHasKey('uuid', $authors[0]);
        $this->assertArrayHasKey('name', $authors[0]);
        $this->assertArrayHasKey('created_at', $authors[0]);
        $this->assertArrayHasKey('updated_at', $authors[0]);
    }

    public function testEmptyListAuthorsViewModel(): void
    {
        $viewModel = new ListAuthorsViewModel([]);

        $authors = $viewModel->render();

        $this->assertIsArray($authors);
        $this->assertCount(0, $authors);
    }
}
