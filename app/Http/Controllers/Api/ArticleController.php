<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\{ArticleCollection, ArticleResource};
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// use Illuminate\Http\Request;

class ArticleController extends Controller
{
  /** SHOW */
  public function show(Article $article): ArticleResource
  {
    return ArticleResource::make($article);
  }

  /** INDEX */
  public function index(): ArticleCollection
  {
    return ArticleCollection::make(Article::all());
  }

  /** STORE */
  public function store(SaveArticleRequest $request): ArticleResource
  {
    $article = Article::create($request->validated());

    return ArticleResource::make($article);
  }

  /** UPDATE */
  public function update(SaveArticleRequest $request, Article $article): ArticleResource
  {
    $article->update($request->validated());

    return ArticleResource::make($article);
  }

  /** DESTROY */
  public function destroy(Article $article): Response
  {
    $article->delete();

    return response()->noContent();
  }
}
