<?php
namespace Grimzy\SecurityJsonServiceProvider\Tests\Misc;

use Grimzy\SecurityJsonServiceProvider\Tests\AbstractTestCase;

class UsernamePasswordParametersTest extends AbstractTestCase
{
    public function testDifferentUsernameParameter()
    {
        $default_username_parameter = 'username';
        $new_username_parameter = 'foo';

        $content = [$default_username_parameter => 'admin', 'password' => 'foo'];
        $code = $this->getCode($content, ['username_parameter' => $new_username_parameter]);
        $this->assertTrue(401 === $code);

        $content = [$new_username_parameter => 'admin', 'password' => 'foo'];
        $code = $this->getCode($content, ['username_parameter' => $new_username_parameter]);
        $this->assertTrue(200 === $code);
    }

    public function testDifferentPasswordParameter()
    {
        $default_password_parameter = 'password';
        $new_password_parameter = 'bar';

        $content = [$default_password_parameter => 'admin', 'password' => 'foo'];
        $code = $this->getCode($content, ['password_parameter' => $new_password_parameter]);
        $this->assertTrue(401 === $code);

        $content = ['username' => 'admin', $new_password_parameter => 'foo'];
        $code = $this->getCode($content, ['password_parameter' => $new_password_parameter]);
        $this->assertTrue(200 === $code);
    }

    private function getCode(array $content = [], array $options = [])
    {
        $app = $this->createApplication($options);
        $client = parent::createClient($app);

        $crawler = $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($content));
        $response = $client->getResponse();
        return $response->getStatusCode();
    }
}