<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\{ArticleCollection, ArticleResource};
use App\Models\Article;
// use Illuminate\Http\Request;

class ArticleController extends Controller {
  public function show(Article $article): ArticleResource {
    return ArticleResource::make($article);
  }

  public function index() {
    return ArticleCollection::make(Article::all());
  }
}
