<?php

namespace Tests\Feature;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\ValidateJsonApiDocument;

class ValidateJsonApiDocumentTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();

    Route::any('test_route', fn () => 'OK')->middleware(ValidateJsonApiDocument::class);
  }

  /** @test */
  public function only_accepts_valid_json_api_documents(): void
  {
    $this->postJson('test_route', [
      'data' => [
        'type' => 'string',
        'attributes' => ['string' => 'string']
      ]
    ])->assertSuccessful();

    $this->patchJson('test_route', [
      'data' => [
        'id' => 'string',
        'type' => 'string',
        'attributes' => ['string' => 'string']
      ]
    ])->assertSuccessful();
  }

  /** @test */
  public function data_is_required(): void
  {
    $this->postJson('test_route', [])
      ->assertJsonApiValidationErrors('data');

    $this->patchJson('test_route', [])
      ->assertJsonApiValidationErrors('data');
  }

  /** @test */
  public function data_must_be_an_array(): void
  {
    $this->postJson('test_route', [
      'data' => 'string'
    ])->assertJsonApiValidationErrors('data');

    $this->patchJson('test_route', [
      'data' => 'string'
    ])->assertJsonApiValidationErrors('data');
  }

  /** @test */
  public function data_type_is_required(): void
  {
    $this->postJson('test_route', [
      'data' => [
        'attributes' => []
      ]
    ])->assertJsonApiValidationErrors('data.type');

    $this->patchJson('test_route', [
      'data' => [
        'attributes' => []
      ]
    ])->assertJsonApiValidationErrors('data.type');
  }

  /** @test */
  public function data_type_must_be_a_string(): void
  {
    $this->postJson('test_route', [
      'data' => [
        'type' => 1,
        'attributes' => ['string' => 'string']
      ]
    ])->assertJsonApiValidationErrors('data.type');

    $this->patchJson('test_route', [
      'data' => [
        'type' => 1,
        'attributes' => ['string' => 'string']
      ]
    ])->assertJsonApiValidationErrors('data.type');
  }

  /** @test */
  public function data_attributes_is_required(): void
  {
    $this->postJson('test_route', [
      'data' => [
        'type' => 'string',
      ]
    ])->assertJsonApiValidationErrors('data.attributes');

    $this->patchJson('test_route', [
      'data' => [
        'type' => 'string'
      ]
    ])->assertJsonApiValidationErrors('data.attributes');
  }

  /** @test */
  public function data_attributes_must_be_an_array(): void
  {
    $this->postJson('test_route', [
      'data' => [
        'type' => 'string',
        'attributes' => 'string'
      ]
    ])->assertJsonApiValidationErrors('data.attributes');

    $this->patchJson('test_route', [
      'data' => [
        'type' => 'string',
        'attributes' => 'string'
      ]
    ])->assertJsonApiValidationErrors('data.attributes');
  }

  /** @test */
  public function data_id_is_required(): void
  {
    $this->patchJson('test_route', [
      'data' => [
        'type' => 'string',
        'attributes' => ['string' => 'string']
      ]
    ])->assertJsonApiValidationErrors('data.id');
  }

  /** @test */
  public function data_id_must_be_a_string(): void
  {
    $this->patchJson('test_route', [
      'data' => [
        'id' => 1,
        'type' => 'string',
        'attributes' => ['string' => 'string']
      ]
    ])->assertJsonApiValidationErrors('data.id');
  }
}
