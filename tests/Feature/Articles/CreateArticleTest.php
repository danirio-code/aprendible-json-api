<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
// use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Article;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function can_create_articles(): void
  {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo artículo',
      'slug' => 'nuevo-articulo',
      'content' => 'Contenido del artículo',
      'user_id' => $user->id,
    ])->assertCreated();

    $article = Article::first();

    $response->assertHeader('Location', route('api.v1.articles.show', $article));


    $response->assertExactJson([
      'data' =>
      [
        'type' =>
        'articles',
        'id' => (string)$article->getRouteKey(),
        'attributes' =>
        [
          'title' => 'Nuevo artículo',
          'slug' => 'nuevo-articulo',
          'content' => 'Contenido del artículo'
        ],
        'links' => [
          'self' => route('api.v1.articles.show', $article)
        ]
      ]
    ]);
  }

  /** @test */
  public function guests_cannot_create_articles(): void
  {
    $this->postJson(route('api.v1.articles.store'))
      ->assertUnauthorized();

    $this->assertDatabaseCount('articles', 0);
  }

  /** @test */
  public function title_is_required(): void
  {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.v1.articles.store'), [
      'slug' => 'nuevo-artículo',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('title');
  }

  /** @test */
  public function title_must_be_at_least_4_characters(): void
  {
    Sanctum::actingAs(User::factory()->create());


    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nue',
      'slug' => 'nuevo-artículo',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('title');
  }

  /** @test */
  public function slug_is_required(): void
  {
    Sanctum::actingAs(User::factory()->create());


    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_be_unique(): void
  {
    Sanctum::actingAs(User::factory()->create());

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
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => '$%&',
      'content' => 'Contenido del artículo',
    ])->assertJsonApiValidationErrors('slug');
  }

  /** @test */
  public function slug_must_not_contain_underscores(): void
  {
    Sanctum::actingAs(User::factory()->create());

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
    Sanctum::actingAs(User::factory()->create());

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
    Sanctum::actingAs(User::factory()->create());

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
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.v1.articles.store'), [
      'title' => 'Nuevo Artículo',
      'slug' => 'nuevo-artículo',
    ])->assertJsonApiValidationErrors('content');
  }
}
