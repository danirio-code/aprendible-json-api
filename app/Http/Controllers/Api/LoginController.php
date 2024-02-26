<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
  /**
   * Handle the incoming request.
   * @throws ValidationException
   */
  public function __invoke(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
      'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'email' => [__('auth.failed')],
      ]);
    }
    // generate the token
    $plain_text_token = $user->createToken($request->device_name, ['articles:create'])->plainTextToken;

    return response()->json([
      'plain-text-token' => $plain_text_token,
    ], 201);
  }
}
