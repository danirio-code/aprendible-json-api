<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
// use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteArticleTest extends TestCase
{
  use RefreshDatabase;


  /** @test */
  public function guest_cannot_delete_articles(): void
  {
    $article = Article::factory()->create();

    $this->deleteJson(route('api.v1.articles.destroy', $article))
      ->assertUnauthorized();
  }

  /** @test */
  public function can_delete_articles(): void
  {
    $article = Article::factory()->create();

    Sanctum::actingAs($article->user);

    $this->deleteJson(route('api.v1.articles.destroy', $article))
      ->assertNoContent();

    $this->assertDatabaseCount('articles', 0);
  }
}
