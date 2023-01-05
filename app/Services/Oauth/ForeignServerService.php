<?php

namespace App\Services\Oauth;

use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForeignServerService
{
    protected string $redirectUrl = "http://192.168.1.14/kit/public/";
    protected string $callbackUrl = "http://localhost:4000/api/callback";
    protected Client $redirectClient;

    public function __construct()
    {
        $this->redirectClient = new Client([
            "base_uri" => $this->redirectUrl,
            "timeout" => 60,
        ]);
    }

    public function redirectToForeignOauthServer(): RedirectResponse
    {
        $state = Str::random(40);
        $code_verifier = Str::random(128);
        $codeChallenge = strtr(rtrim(
            base64_encode(hash("sha256", $code_verifier, true)),
            "="
        ), "+/", "-_");

        session([
            "state" => $state,
            "code_verifier" => $code_verifier,
        ]);

        $query = http_build_query([
            "response_type" => "code",
            "client_id" => env("PKCE_AUTH_CLIENT_ID"),
            "redirect_uri" => $this->callbackUrl,
            "scope" => "get-name get-username get-email",
            "state" => $state,
            "code_challenge" => $codeChallenge,
            "code_challenge_method" => "S256",
            "prompt" => "login", // "none", "consent", or "login"
        ]);
        
        return redirect($this->redirectUrl . "oauth/authorize?" . $query);
    }

    public function getAccessToken(Request $request): array
    {
        $state = $request->session()->pull("state");
        $codeVerifier = $request->session()->pull("code_verifier");

        throw_unless(
            strlen($state) > 0 && $state === $request->state && $codeVerifier > 0,
            InvalidArgumentException::class
        );

        $params = [
            "grant_type" => "authorization_code",
            "client_id" => env("PKCE_AUTH_CLIENT_ID"),
            "redirect_uri" => $this->callbackUrl,
            "code_verifier" => $codeVerifier,
            "code" => $request->code,
        ];
        $response = $this->redirectClient->request("POST", "oauth/token", ["form_params" => $params]);

        return json_decode($response->getBody(), true);
    }

    public function getUserFromRedirectClient(array $response): array
    {
        $getUserFromRedirectClient = $this->redirectClient->request("GET", "api/user", [
            "headers" => [
                "Accept" => "application/json",
                "Authorization" => "Bearer " . $response["access_token"],
            ]
        ]);

        return json_decode($getUserFromRedirectClient->getBody(), true);
    }
}
