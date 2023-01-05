<?php

namespace App\Services\Oauth;

use App\Events\UserLoggedInEvent;
use App\Exceptions\NotFoundException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Nyholm\Psr7\ServerRequest as Psr7ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class AuthService extends OtpService
{
    use HandlesOAuthErrors;

    protected AccessTokenController $accessTokenController;
    protected Psr7ServerRequest $psrRequest;

    /**
     * Create a new controller instance.
     *
     * @param  \League\OAuth2\Server\AuthorizationServer  $server
     * @param  \Laravel\Passport\TokenRepository  $tokens
     * @return void
     */
    public function __construct(
        ServerRequestInterface $psrRequest,
        AccessTokenController $accessTokenController,
    ) {
        $this->psrRequest = $psrRequest;
        $this->accessTokenController = $accessTokenController;
    }

    public function login(array $attributes): mixed
    {
        if (!Auth::guard("web")->attempt($attributes)) {
            throw new NotFoundException("User Not Found.");
        }

        $user = Auth::guard("web")->user();

        if ($user->is2Fa) {
            $hash = Hash::make(rand(000000, 999999));
            Cache::put(["hashKey.$hash" => $hash], now()->addSeconds(120));
            $user->hash = $hash;
            $user->save();
            return [
                "hash" => $hash,
                "code" => $this->getUserSecret($user),
            ];
        }

        $accessTokenData = $this->generateToken($attributes);
        $user->access_token = $accessTokenData["access_token"];
        $user->save();

        return $accessTokenData;
    }

    public function logout(): void
    {
        $user = Auth::user();
        $tokenId = $user->token()->id;
        $this->accessTokenController->revokeToken($tokenId);
        $user->access_token = "";
        $user->save();
    }

    public function generateToken(array $attributes): array
    {
        $payload = [
            "grant_type" => "password",
            "client_id" => env("OAUTH_CLIENT_ID"),
            "client_secret" => env("OAUTH_CLIENT_SECRET"),
            "username" => $attributes["email"],
            "password" => $attributes["password"],
            "scope" => "*"
        ];

        $data = $this->accessTokenController->issueToken($this->psrRequest->withParsedBody($payload));
        $accessTokenData = json_decode((string) $data->getContent(), true);
        unset($accessTokenData["refresh_token"]);

        return $accessTokenData;
    }

    public function verifyOtp(Request $request): mixed
    {
        $hash = Cache::get("hashKey.$request->hash");
        if (!$hash) {
            throw new NotFoundException();
        }
        $user = User::where("hash", $hash)->firstOrFail();

        $accessTokenData = $this->customAccessToken($hash, $request);

        if (isset($accessTokenData["access_token"])) {
            $user->access_token = $accessTokenData["access_token"];
            $user->save();

            UserLoggedInEvent::dispatch($user);
        }

        return $accessTokenData;
    }

    public function customAccessToken(string $hash, Request $request): array
    {
        $payload = [
            "grant_type" => "custom_grant",
            "client_id" => env("OAUTH_CLIENT_ID"),
            "client_secret" => env("OAUTH_CLIENT_SECRET"),
            "hash" => $hash,
            "otp" => $request->code
        ];

        $data = $this->accessTokenController->issueToken($this->psrRequest->withParsedBody($payload));
        return json_decode((string) $data->getContent(), true);
    }
}
