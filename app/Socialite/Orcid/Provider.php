<?php

namespace App\Socialite\Orcid;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider {
    public const IDENTIFIER = 'ORCID';

    protected $scopes = ['/authenticate', 'openid'];

    protected $scopeSeparator = ' ';

    protected $base_uri = 'https://orcid.org/';
    protected $api_uri = 'https://orcid.org/';

    protected function getAuthUrl($state): string {
        return $this->buildAuthUrlFromBase($this->base_uri . 'oauth/authorize', $state);
    }

    protected function getTokenUrl(): string {
        return $this->base_uri . 'oauth/token';
    }

    /*
    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token, $orcid) {
        $response = $this->getHttpClient()->get($this->api_uri . '/v3.0/' . $orcid, [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user) {
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['username'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'avatar'   => $user['avatar_url'],
        ]);
    }

    public static function additionalConfigKeys(): array {
        return ['instance_uri'];
    }
}
