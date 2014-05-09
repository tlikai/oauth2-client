<?php

namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Token\AccessToken as AccessToken;

class QQ extends AbstractProvider
{
    public $scopes = array(
        'get_user_info',
    );

    public $responseType = 'string';

    public function urlAuthorize()
    {
        return 'https://graph.qq.com/oauth2.0/authorize';
    }

    public function urlAccessToken()
    {
        return 'https://graph.qq.com/oauth2.0/token';
    }

    public function urlUserDetails(\League\OAuth2\Client\Token\AccessToken $token)
    {
        return 'https://graph.qq.com/user/get_user_info?' . http_build_query([
            'access_token' => $token->accessToken,
            'oauth_consumer_key' => $this->clientId,
            'openid' => $this->getUserUid($token),
        ]);
    }

    public function userDetails($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $response = (array) $response;
        $user = new User;
        $uid = $this->getUserUid($token);
        $name = $response['nickname'];
        $imageUrl = (isset($response['figureurl_qq_2'])) ? $response['figureurl_qq_2'] : null;

        $user->exchangeArray(array(
            'uid' => $uid,
            'name' => $name,
            'imageurl' => $imageUrl,
        ));

        return $user;
    }

    public function getUserUid(AccessToken $token)
    {
        static $response = null;

        if ($response == null) {
            $client = $this->getHttpClient();
            $client->setBaseUrl('https://graph.qq.com/oauth2.0/me?access_token=' . $token);
            $request = $client->get()->send();
            if (preg_match('/callback\((.+?)\)/', $request->getBody(), $match)) {
                $response = json_decode($match[1]);
            }
        }

        return $this->userUid($response, $token);
    }

    public function userUid($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $token->uid = $response->openid;
        return $response->openid;
    }

    public function userEmail($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return isset($response->email) && $response->email ? $response->email : null;
    }

    public function userScreenName($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->name;
    }
}
