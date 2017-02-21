<?php
namespace Grimzy\SecurityJsonServiceProvider\Tests;

use Grimzy\SecurityJsonServiceProvider\SecurityJsonServiceProvider;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @param bool $options
     * @return Application
     */
    protected function createApplication($options = true)
    {
//        require_once __DIR__ . '/../vendor/autoload.php';

        $app = new Application(['debug' => true]);

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
            return new InMemoryUserProvider($users);
        };

        $app->register(new SecurityServiceProvider());
        $app->register(new SecurityJsonServiceProvider());

        $app['security.default_encoder'] = function () {
            return new PlaintextPasswordEncoder();
        };


        $app->post('/api/login', function (Request $request) use ($app) {
            return new Response('success post');
        });

        if (isset($options['post_only']) && false === $options['post_only']) {
            $app->get('/api/login', function (Request $request) use ($app) {
                return new Response('success get');
            });
        }

        return $app;
    }

    /**
     * Creates a Client.
     *
     * @param Application $app
     * @param array $server Server parameters
     *
     * @return Client A Client instance
     */
    protected function createClient(Application $app, array $server = array())
    {
        if (!class_exists('Symfony\Component\BrowserKit\Client')) {
            throw new \LogicException('Component "symfony/browser-kit" is required by WebTestCase.' . PHP_EOL . 'Run composer require symfony/browser-kit');
        }

        return new Client($app, $server);
    }
}