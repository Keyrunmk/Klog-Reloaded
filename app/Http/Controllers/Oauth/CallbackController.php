<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CallbackController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $response = Http::asForm()->post("192.168.1.24/kit/public/oauth/token", [
            "grant_type" => "authorization_code",
            "client_id" => env("AUTH_CLIENT_ID"),
            "client_secret" => env("AUTH_CLIENT_SECRET"),
            "redirect_uri" => "http://localhost:4000/api/callback",
            "code" => $request->code,
        ]);

        return $response->json();
    }
}
