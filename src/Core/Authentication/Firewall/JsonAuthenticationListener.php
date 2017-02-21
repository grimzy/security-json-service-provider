<?php
namespace Grimzy\SecurityJsonServiceProvider\Core\Authentication\Firewall;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class JsonAuthenticationListener implements ListenerInterface
{
    private $tokenStorage;
    private $authenticationManager;
    private $providerKey;
    private $options;
    private $logger;

    public function __construct(TokenStorageInterface $tokenStorage,
                                AuthenticationManagerInterface $authenticationManager,
                                $providerKey,
                                array $options = [],
                                LoggerInterface $logger = null)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
        $this->options = array_merge(array(
            'username_parameter' => 'username',
            'password_parameter' => 'password',
            'post_only' => true,
            'json_only' => true,
        ), $options);
        $this->logger = $logger;
    }

    /**
     * JSON Authentication handler
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        /** @var Request $request */
        $request = $event->getRequest();

        if ($this->options['post_only'] && !$request->isMethod(Request::METHOD_POST)) {
            throw new MethodNotAllowedHttpException([Request::METHOD_POST], 'Method Not Allowed', null, Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $username = AuthenticationProviderInterface::USERNAME_NONE_PROVIDED;
        $password = null;

        if (false !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $username = trim(isset($data["{$this->options['username_parameter']}"]) ? $data["{$this->options['username_parameter']}"] : $username);
            $password = isset($data["{$this->options['password_parameter']}"]) ? $data["{$this->options['password_parameter']}"] : null;
        }

        if (true !== $this->options['json_only'] && AuthenticationProviderInterface::USERNAME_NONE_PROVIDED === $username) {
            $username = trim($request->request->get($this->options['username_parameter'], $username));
            $password = $request->request->get($this->options['password_parameter'], null);

            if (true !== $this->options['post_only'] && AuthenticationProviderInterface::USERNAME_NONE_PROVIDED === $username) {
                $username = trim($request->get($this->options['username_parameter'], $username));
                $password = $request->get($this->options['password_parameter'], null);
            }
        }

        try {
            $token = $this->authenticationManager->authenticate(new UsernamePasswordToken($username, $password, $this->providerKey));
            $this->tokenStorage->setToken($token);

            if (null !== $this->logger) {
                $this->logger->info(sprintf('"%s" has retrieved a token', $token->getUsername()));
            }

        } catch (AuthenticationException $e) {
            if (null !== $this->logger) {
                $this->logger->info(sprintf('Authentication request failed: %s', $e->getMessage()));
            }
            // throwing an authentication exception returns the default entry point
            throw $e;
        }
    }
}