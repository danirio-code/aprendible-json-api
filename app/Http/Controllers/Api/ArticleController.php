<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
// use Illuminate\Support\Str;
// use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\{ArticleCollection, ArticleResource};

// use Illuminate\Http\Request;

class ArticleController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:sanctum')
      ->only(['store', 'update', 'destroy']);

    // $this->middleware('can:articles-create')->only('store');
  }

  /** SHOW */
  public function show(Article $article): ArticleResource
  {
    return ArticleResource::make($article);
  }

  /** INDEX */
  public function index(): ArticleCollection
  {
    $articles = Article::allowedSorts(['title', 'content']);

    return ArticleCollection::make($articles->jsonPaginate());
  }

  /** STORE */
  public function store(SaveArticleRequest $request): ArticleResource
  {
    $user = $request->user();

    if (!$user->tokenCan('articles:create')) {
      abort(Response::HTTP_FORBIDDEN);
    }

    $article = Article::create($request->validated() + ['user_id' => auth()->id()]);

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
