# Silex Security JSON Service Provider

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