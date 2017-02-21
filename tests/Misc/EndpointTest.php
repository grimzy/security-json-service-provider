<?php
require_once __DIR__ . '/../AbstractTestCase.php';
require_once __DIR__ . '/../TestAuthenticationEntryPoint.php';

class EndpointTest extends AbstractTestCase
{
    public function testEndPointOverride()
    {
        $app = $this->createApplication();

        $app['security.entry_point.json'] = function () {
            return new TestAuthenticationEntryPoint();
        };

        $client = $this->createClient($app);
        $crawler = $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['username' => 'admin', 'password' => 'wrong']));
        $response = $client->getResponse();

        $this->assertTrue(401 === $response->getStatusCode());
        $this->assertTrue('overridden' === $response->getContent());
    }
}