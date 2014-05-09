<?php

namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Entity\User;

class Weibo extends AbstractProvider
{
    public $scopes = array('email');

    public function urlAuthorize()
    {
        return 'https://api.weibo.com/oauth2/authorize';
    }

    public function urlAccessToken()
    {
        return 'https://api.weibo.com/oauth2/access_token';
    }

    public function urlUserDetails(\League\OAuth2\Client\Token\AccessToken $token)
    {
        return 'https://api.weibo.com/2/users/show.json?access_token='.$token.'&uid='.$token->uid;
    }

    public function userDetails($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $response = (array) $response;
        $user = new User;
        $uid = $response['id'];
        $name = $response['name'];
        $description = $response['description'];
        $location = $response['location'];
        $imageUrl = $response['profile_image_url'];
        $urls = array('Weibo' => 'http://weibo.com/' . $response['profile_url']);

        $user->exchangeArray(array(
            'uid' => $uid,
            'name' => $name,
            'description' => $description,
            'location' => $location,
            'imageurl' => $imageUrl,
            'urls' => $urls,
        ));

        return $user;
    }

    public function userUid($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->id;
    }

    public function userEmail($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return null;
    }

    public function userScreenName($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->screen_name;
    }
}
