<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function can_create_articles(): void
  {
    $response = $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo artículo',
      'slug' => 'nuevo-artículo',
      'content' => 'Contenido del artículo',
    ])->assertCreated();

    $article = Article::first();

    $response->assertHeader(
      'Location',
      route('api.v1.articles.show', $article)
    );

    $response->assertExactJson([
      'data' => [
        'type' => 'articles',
        'id' => (string) $article->getRouteKey(),
        'attributes' => [
          'title' => $article->title,
          'slug' => $article->slug,
          'content' => $article->content,
        ],
        'links' => [
          'self' => route('api.v1.articles.show', $article)
        ]
      ]
    ]);
  }

  /** @test */
  public function title_is_required(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'slug' => 'nuevo-artículo',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('title');
  }

  /** @test */
  public function slug_is_required(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function content_is_required(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => 'nuevo-artículo',
    ])->assertJsonApiValidationErrors('content');
  }

  /** @test */
  public function title_must_be_at_least_4_characters(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nue',
      'slug' => 'nuevo-artículo',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('title');
  }
}
