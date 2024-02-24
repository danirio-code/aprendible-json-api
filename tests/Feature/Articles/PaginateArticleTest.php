<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaginateArticleTest extends TestCase
{
  use RefreshDatabase;


  /** @test */
  public function can_paginate_articles(): void
  {
    $articles = Article::factory(6)->create();

    // /articles?page[size]=2&page[number]=2
    $url = route('api.v1.articles.index', [
      'page' => [
        'size' => 2,
        'number' => 2,
      ],
    ]);

    $response = $this->getJson($url);

    $response->assertSee([
      $articles[2]->title,
      $articles[3]->title,
    ]);

    $response->assertDontSee([
      $articles[0]->title,
      $articles[1]->title,
      $articles[4]->title,
      $articles[5]->title,
    ]);

    $response->assertJsonStructure([
      'links' => ['first', 'last', 'prev', 'next'],
    ]);

    $first_link = urldecode($response->json('links.first'));
    $last_link = urldecode($response->json('links.last'));
    $prev_link = urldecode($response->json('links.prev'));
    $next_link = urldecode($response->json('links.next'));

    $this->assertStringContainsString('page[size]=2', $first_link);
    $this->assertStringContainsString('page[number]=1', $first_link);

    $this->assertStringContainsString('page[size]=2', $last_link);
    $this->assertStringContainsString('page[number]=3', $last_link);

    $this->assertStringContainsString('page[size]=2', $prev_link);
    $this->assertStringContainsString('page[number]=1', $prev_link);

    $this->assertStringContainsString('page[size]=2', $next_link);
    $this->assertStringContainsString('page[number]=3', $next_link);
  }

  /** @test */
  public function can_paginate_and_sort_articles(): void
  {
    Article::factory()->create(['title' => 'C Title']);
    Article::factory()->create(['title' => 'A Title']);
    Article::factory()->create(['title' => 'B Title']);

    // /articles?sort=title&page[size]=1&page[number]=2
    $url = route('api.v1.articles.index', [
      'sort' => 'title',
      'page' => [
        'size' => 1,
        'number' => 2,
      ],
    ]);

    $response = $this->getJson($url);

    $response->assertSee([
      'B Title',
    ]);

    $response->assertDontSee([
      'A Title',
      'C Title',
    ]);

    $first_link = urldecode($response->json('links.first'));
    $last_link = urldecode($response->json('links.last'));
    $prev_link = urldecode($response->json('links.prev'));
    $next_link = urldecode($response->json('links.next'));

    $this->assertStringContainsString('sort=title', $first_link);
    $this->assertStringContainsString('sort=title', $last_link);
    $this->assertStringContainsString('sort=title', $prev_link);
    $this->assertStringContainsString('sort=title', $next_link);
  }
}
