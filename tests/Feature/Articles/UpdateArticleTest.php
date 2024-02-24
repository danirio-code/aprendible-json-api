<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateArticleTest extends TestCase
{
  use RefreshDatabase;


  /** @test */
  public function can_update_articles(): void
  {
    $article = Article::factory()->create();

    $response = $this->patchJson(route('api.v1.articles.update', $article), [
      'title' => 'Updated Article',
      'slug' => $article->slug,
      'content' => 'Updated content',
    ])->assertOk();

    $response->assertHeader(
      'Location',
      route('api.v1.articles.show', $article)
    );

    $response->assertExactJson([
      'data' => [
        'type' => 'articles',
        'id' => (string) $article->getRouteKey(),
        'attributes' => [
          'title' => 'Updated Article',
          'slug' => $article->slug,
          'content' => 'Updated content',
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
    $article = Article::factory()->create();
    $this->patchJson(route('api.v1.articles.update', $article), [
      'slug' => 'updated-article',
      'content' => 'Updated content',
    ])->assertJsonApiValidationErrors('title');
  }

  /** @test */
  public function title_must_be_at_least_4_characters(): void
  {
    $article = Article::factory()->create();
    $this->patchJson(route('api.v1.articles.update', $article), [
      'title' => 'Nue',
      'slug' => 'updated-article',
      'content' => 'Updated content',
    ])->assertJsonApiValidationErrors('title');
  }

  /** @test */
  public function slug_is_required(): void
  {
    $article = Article::factory()->create();
    $this->patchJson(route('api.v1.articles.update', $article), [
      'title' => 'Updated Article',
      'content' => 'Updated content',
    ])->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_be_unique(): void
  {
    $article1 = Article::factory()->create();
    $article2 = Article::factory()->create();

    $this->patchJson(route('api.v1.articles.update', $article1), [
      'title' => 'Nuevo Artículo',
      'slug' => $article2->slug,
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_only_contain_letters_numbers_and_dashes(): void
  {
    $article = Article::factory()->create();

    $this->patchJson(route('api.v1.articles.update', $article), [
      'title' => 'Nuevo Artículo',
      'slug' => '$%&',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_not_contain_underscores(): void
  {
    $article = Article::factory()->create();

    $this->patchJson(route('api.v1.articles.update', $article), [
      'title' => 'Nuevo Artículo',
      'slug' => 'with_underscore',
      'content' => 'Contenido del artículo',
    ])->assertSee(trans('validation.no_underscore', ['attribute' => 'slug']))
      ->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_not_start_with_dashes(): void
  {
    $article = Article::factory()->create();

    $this->patchJson(route('api.v1.articles.update', $article), [
      'title' => 'Nuevo Artículo',
      'slug' => '-starts-with-dash',
      'content' => 'Contenido del artículo',
    ])->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']))
      ->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_not_end_with_dashes(): void
  {
    $article = Article::factory()->create();

    $this->patchJson(route('api.v1.articles.update', $article), [
      'title' => 'Nuevo Artículo',
      'slug' => 'ends-with-dash-',
      'content' => 'Contenido del artículo',
    ])->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']))
      ->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function content_is_required(): void
  {
    $article = Article::factory()->create();
    $this->patchJson(route('api.v1.articles.update', $article), [
      'title' => 'Updated Article',
      'slug' => 'updated-article',
    ])->assertJsonApiValidationErrors('content');
  }
}
