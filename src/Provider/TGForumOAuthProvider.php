<?php

namespace App\Provider;

use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class TGForumOAuthProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    public $host = 'https://tgstation13.org/phpBB/app.php/tgapi';

    public function getBaseAuthorizationUrl(): string
    {
        return $this->host."/oauth/auth";
    }
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->host.'/oauth/token';
    }
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->host."/user/me";
    }
    protected function getDefaultScopes(): array
    {
        return ['user','user.linked_accounts'];
    }
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new Exception("Invalid response");
        }
    }
    protected function createResourceOwner(array $response, AccessToken $token): TGForumResourceOwner
    {
        return new TGForumResourceOwner($response);
    }

}
