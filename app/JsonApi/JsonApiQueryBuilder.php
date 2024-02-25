<?php

namespace App\JsonApi;

use Illuminate\Support\Str;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class JsonApiQueryBuilder
{
  public function allowedSorts(): Closure
  {
    return function (array $allowedSorts) {
      /** @var Builder $this */

      if (request()->filled('sort')) {
        $sortFields = explode(',', request()->input('sort'));

        foreach ($sortFields as $sortField) {
          $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';

          $sortField = Str::of($sortField)->ltrim('-');

          abort_unless(in_array($sortField, $allowedSorts), 400, 'Invalid sort field');

          $this->orderBy($sortField, $sortDirection);
        }
      }

      return $this;
    };
  }

  public function jsonPaginate(): Closure
  {
    return function () {
      /** @var Builder $this */

      return $this->paginate(
        $perPage = request('page.size', 15),
        $columns = ['*'],
        $pageName = 'page[number]',
        $page = request('page.number', 1)
      )->appends(request()->only('sort', 'page.size'));
    };
  }
}
