<?php

namespace App\Grants;

use Laravel\Passport\Bridge\User;
use DateInterval;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\RequestAccessTokenEvent;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * Custom grant class.
 */
class CustomGrant extends AbstractGrant
{
    protected OtpVerify $otpVerifier;

    public function __construct(OtpVerify $otpVerifier)
    {
        $this->otpVerifier = $otpVerifier;
    }

    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    ): mixed {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request, $this->defaultScope));
        $user = $this->validateUser($request, $client);

        // Finalize the requested scopes
        $finalizedScopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());

        // Issue and persist new access token
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $finalizedScopes);
        $this->getEmitter()->emit(new RequestAccessTokenEvent(RequestEvent::ACCESS_TOKEN_ISSUED, $request, $accessToken));
        $responseType->setAccessToken($accessToken);

        return $responseType;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ClientEntityInterface  $client
     *
     * @throws OAuthServerException
     *
     * @return UserEntityInterface
     */
    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client): UserEntityInterface
    {
        $otp = $this->getRequestParameter('otp', $request);
        $hash = $this->getRequestParameter('hash', $request);

        if (is_null($otp)) {
            throw OAuthServerException::invalidRequest('otp');
        }

        $this->otpVerifier->verify($hash, $otp);

        if (!\is_string($hash)) {
            throw OAuthServerException::invalidRequest('hash');
        }

        $user = $this->getUserEntityByUserHash(
            $hash,
            $client
        );

        if ($user instanceof UserEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

    public function getUserEntityByUserHash(string $hash, ClientEntityInterface $clientEntity)
    {
        $provider = $clientEntity->provider ?: config('auth.guards.api.provider');

        if (is_null($model = config('auth.providers.' . $provider . '.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        $user = (new $model)->where('hash', $hash)->first();

        if (is_null($user)) {
            return;
        }

        return new User($user->getAuthIdentifier());
    }

    public function getIdentifier()
    {
        return 'custom_grant';
    }
}
