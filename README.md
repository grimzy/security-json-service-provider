# Silex Security JSON Service Provider

[![Build Status](https://img.shields.io/travis/ARCANEDEV/LogViewer.svg?style=flat-square)](https://travis-ci.org/grimzy/security-json-service-provider)
[![Packagist](https://img.shields.io/packagist/v/grimzy/security-json-service-provider.svg?style=flat-square)](https://packagist.org/packages/grimzy/security-json-service-provider)
[![Packagist](https://img.shields.io/packagist/dt/grimzy/security-json-service-provider.svg?style=flat-square)](https://packagist.org/packages/grimzy/security-json-service-provider)
[![Packagist Pre Release](https://img.shields.io/packagist/vpre/grimzy/security-json-service-provider.svg?style=flat-square)](https://packagist.org/packages/grimzy/security-json-service-provider)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](LICENSE)



This Security factory provides a cookie-less replacement for `form_login` which cannot be used .

Since they rely on cookies, the `switch_user` and `logout` config options are not supported with this Security factory.

**Security advisory:** Although you are not forced to, it is highly advised to use HTTPS.

## Installation

Using command line:

```shell
composer require grimzy/security-json-service-provider:1.0^
```

Or adding to composer.json:

```
"grimzy/security-json-service-provider:1.0^"
```

## Usage

Configure firewalls:

```php
$app['security.firewalls'] = [
  'login' => [
    'pattern' => '^/api/login',
    'anonymous' => true,
    'stateless' => true,
    'json' => [
      // Default configuration
      'username_parameter' => 'username',
      'password_parameter' => 'password',
      'post_only' => true,
      'json_only' => true
    ]
  ],

  'secured' => [
    'pattern' => '^.*$',
    'stateless' => true,
    'token' => true	
  ],
];
```

Add a users provider:

```php
$app['users'] = function () use ($app) {
  return new InMemoryUserProvider([
    'admin' => [
      'roles' => ['ROLE_ADMIN'],
      'password' => '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==',	// foo
      'enabled' => true
    ],
  ]);
};
```

Example configuration:

```php
$app['security.firewalls' => [
  'login' => [
    'pattern' => '^/api/login',
    'anonymous' => true,
    'stateless' => true,
    'json' => [
      // Default configuration
      'username_parameter' => 'username',
      'password_parameter' => 'password',
      'post_only' => true,
      'json_only' => true
    ]
  ],

  'secured' => [
    'pattern' => '^.*$',
    'stateless' => true,
    'token' => true
  ],
]];
```

Register the service providers:

```php
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\SecurityJsonServiceProvider());
```

Define a route (**only accessible after successful authentication**):

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$app->post('/api/login', function(Request $request) use ($app) {
  $user = $app['user'];	// Logged in user
  
  $token = $app['some.token_encoder']->encode($user);
  
  return new JsonResponse([
    'token' => $token
  ]);
};
```
**Note:** if `post_only` is `false`, you can use `$app->get()` instead of `$app->post` when defining your route.

## Override entry point

Create a new class implementing `Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface`:

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GandalfAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response('You shall not pass!', Response::HTTP_UNAUTHORIZED);
    }
}
```

Replace the packaged JsonAuthenticationEntrypoint with the created one:

```php
$app->register(new Silex\Provider\SecurityJsonServiceProvider());

// after registering the provider
$app['security.entry_point.json'] = function () use ($app) {
    return new GandalfAuthenticationEntryPoint();
};
```