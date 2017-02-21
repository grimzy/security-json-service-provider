<?php

abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
{
    protected function createApplication($options = true): \Silex\Application
    {
        require_once __DIR__ . '/../vendor/autoload.php';

        $app = new \Silex\Application(['debug' => true]);

        $app['security.firewalls'] = [
            'login' => [
                'pattern' => '^/api/login',
                'anonymous' => true,
                'stateless' => true,
                'json' => $options
            ]
        ];

        $app['users'] = function () use ($app) {
            $users = [
                'admin' => array(
                    'password' => 'foo',
                    'enabled' => true,
                    'roles' => array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN')
                ),
            ];
            return new \Symfony\Component\Security\Core\User\InMemoryUserProvider($users);
        };

        $app->register(new Silex\Provider\SecurityServiceProvider());
        $app->register(new \Grimzy\SecurityJsonServiceProvider\SecurityJsonServiceProvider());

        $app['security.default_encoder'] = function () {
            return new \Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder();
        };


        $app->post('/api/login', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
            return new \Symfony\Component\HttpFoundation\Response('success post');
        });

        if (isset($options['post_only']) && false === $options['post_only']) {
            $app->get('/api/login', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
                return new \Symfony\Component\HttpFoundation\Response('success get');
            });
        }

        return $app;
    }

    /**
     * Creates a Client.
     *
     * @param \Silex\Application $app
     * @param array $server Server parameters
     *
     * @return \Symfony\Component\HttpKernel\Client A Client instance
     */
    protected function createClient(\Silex\Application $app, array $server = array())
    {
        if (!class_exists('Symfony\Component\BrowserKit\Client')) {
            throw new \LogicException('Component "symfony/browser-kit" is required by WebTestCase.' . PHP_EOL . 'Run composer require symfony/browser-kit');
        }

        return new \Symfony\Component\HttpKernel\Client($app, $server);
    }
}