<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class JsonApiValidationErrorResponse extends JsonResponse
{
  public function __construct(ValidationException $exception, $status = 422)
  {
    $title = 'The given data was invalid';

    $data = $this->formatJsonApiErrors($exception, $title);

    $headers = [
      'Content-Type' => 'application/vnd.api+json'
    ];

    parent::__construct($data, $status, $headers);
  }

  protected function formatJsonApiErrors(ValidationException $exception, string $title): array
  {
    return [
      'errors' => collect($exception->errors())
        ->map(function ($message, $field) use ($title) {
          return [
            'title' => $title,
            'detail' => $message[0],
            'source' => [
              'pointer' => '/' . str_replace('.', '/', $field)
            ]
          ];
        })->values()->toArray()
    ];
  }
}
