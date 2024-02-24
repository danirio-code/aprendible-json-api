<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SortArticleTest extends TestCase
{
  use RefreshDatabase;


  /** @test */
  public function can_sort_articles_by_title(): void
  {
    Article::factory()->create(['title' => 'C Title']);
    Article::factory()->create(['title' => 'A Title']);
    Article::factory()->create(['title' => 'B Title']);

    // /articles?sort=title
    $url = route('api.v1.articles.index', ['sort' => 'title']);

    $this->getJson($url)->assertSeeInOrder([
      'A Title',
      'B Title',
      'C Title',
    ]);
  }

  /** @test */
  public function can_sort_articles_by_title_descending(): void
  {
    Article::factory()->create(['title' => 'C Title']);
    Article::factory()->create(['title' => 'A Title']);
    Article::factory()->create(['title' => 'B Title']);

    // /articles?sort=-title
    $url = route('api.v1.articles.index', ['sort' => '-title']);

    $this->getJson($url)->assertSeeInOrder([
      'C Title',
      'B Title',
      'A Title',
    ]);
  }

  /** @test */
  public function can_sort_articles_by_content(): void
  {
    Article::factory()->create(['content' => 'C Content']);
    Article::factory()->create(['content' => 'A Content']);
    Article::factory()->create(['content' => 'B Content']);

    // /articles?sort=content
    $url = route('api.v1.articles.index', ['sort' => 'content']);

    $this->getJson($url)->assertSeeInOrder([
      'A Content',
      'B Content',
      'C Content',
    ]);
  }

  /** @test */
  public function can_sort_articles_by_content_descending(): void
  {
    Article::factory()->create(['content' => 'C Content']);
    Article::factory()->create(['content' => 'A Content']);
    Article::factory()->create(['content' => 'B Content']);

    // /articles?sort=-content
    $url = route('api.v1.articles.index', ['sort' => '-content']);

    $this->getJson($url)->assertSeeInOrder([
      'C Content',
      'B Content',
      'A Content',
    ]);
  }

  /** @test */
  public function can_sort_articles_by_title_and_content(): void
  {
    Article::factory()->create([
      'title' => 'A Title',
      'content' => 'A Content'
    ]);
    Article::factory()->create([
      'title' => 'B Title',
      'content' => 'B Content'
    ]);
    Article::factory()->create([
      'title' => 'A Title',
      'content' => 'C Content'
    ]);

    // /articles?sort=title,-content
    $url = route('api.v1.articles.index', ['sort' => 'title,-content']);

    $this->getJson($url)->assertSeeInOrder([
      'C Content',
      'A Content',
      'B Content',
    ]);
  }

  /** @test */
  public function cannot_sort_by_unknown_fields(): void
  {
    Article::factory(3)->create();

    // /articles?sort=unknown
    $url = route('api.v1.articles.index', ['sort' => 'unknown']);

    $this->getJson($url)->assertStatus(400);
  }
}
