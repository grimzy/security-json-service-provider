<?php
namespace Grimzy\SecurityJsonServiceProvider\Http\EntryPoint;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class JsonAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response(null, Response::HTTP_UNAUTHORIZED);
    }
}