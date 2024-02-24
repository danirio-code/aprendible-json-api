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
  public function title_must_be_at_least_4_characters(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nue',
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
  public function slug_must_be_unique(): void
  {
    $article = Article::factory()->create();

    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => $article->slug,
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_only_contain_letters_numbers_and_dashes(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => '$%&',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_not_contain_underscores(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => 'with_underscore',
      'content' => 'Contenido del artículo',
    ])->assertSee(trans('validation.no_underscore', ['attribute' => 'slug']))
      ->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_not_start_with_dashes(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => '-starts-with-dash',
      'content' => 'Contenido del artículo',
    ])->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']))
      ->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_not_end_with_dashes(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => 'ends-with-dash-',
      'content' => 'Contenido del artículo',
    ])->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']))
      ->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function content_is_required(): void
  {
    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => 'nuevo-artículo',
    ])->assertJsonApiValidationErrors('content');
  }
}
