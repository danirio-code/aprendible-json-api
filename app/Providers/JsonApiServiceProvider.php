<?php

namespace App\Providers;

// use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class JsonApiServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {

    Builder::macro('allowedSorts', function (array $allowedSorts) {
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
    });
  }
}
