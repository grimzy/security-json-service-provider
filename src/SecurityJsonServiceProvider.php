<?php
namespace Grimzy\SecurityJsonServiceProvider;

use Grimzy\SecurityJsonServiceProvider\Core\Authentication\Firewall\JsonAuthenticationListener;
use Grimzy\SecurityJsonServiceProvider\Http\EntryPoint\JsonAuthenticationEntryPoint;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;

class SecurityJsonServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $app['security.entry_point.json'] = function () use ($app) {
            return new JsonAuthenticationEntryPoint();
        };

        $app['security.authentication_listener.factory.json'] = $app->protect(function ($providerKey, $options) use ($app) {
            $app['security.authentication_provider.' . $providerKey . '.json'] = function () use ($app, $providerKey) {
                // Using the default DaoAuthenticationProvider
                return new DaoAuthenticationProvider(
                    $app['users'],
                    $app['security.user_checker'],  // default user_checker
                    $providerKey,
                    $app['security.encoder_factory'],   // default encoder_factory
                    $app['security.hide_user_not_found']
                );
            };

            // define the authentication listener object
            $app['security.authentication_listener.' . $providerKey . '.json'] = function () use ($app, $providerKey, $options) {
                return new JsonAuthenticationListener(
                    $app['security.token_storage'],
                    $app['security.authentication_manager'],
                    $providerKey,
                    $options,
                    $app['logger']
                );
            };

            return [
                'security.authentication_provider.' . $providerKey . '.json',
                'security.authentication_listener.' . $providerKey . '.json',
                'security.entry_point.json',
                'pre_auth'
            ];
        });
    }
}