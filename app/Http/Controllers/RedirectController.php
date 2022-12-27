<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    protected string $baseUrl = "http://192.168.1.24/kit/public/oauth/authorize";
    protected string $redirectUrl = "http://localhost:4000/api/callback";

    public function login(Request $request): RedirectResponse
    {
        $params = [
            "response_type" => "code",
            "client_id" => env("AUTH_CLIENT_ID"),
            "redirect_uri" => $this->redirectUrl,
            "scope" => "get-name get-username get-email",
            "state" => "",
            "prompt" => "login", // "none", "consent", or "login"
        ];
    
        return redirect($this->baseUrl . http_build_query($params));
    }
}
