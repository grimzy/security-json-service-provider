<?php

abstract class AbstractCredentialsTestCase extends AbstractTestCase
{
    abstract protected function getOptions();

    abstract protected function codesForPostAsForm(): array;

    abstract protected function codesForPostAsJson(): array;

    abstract protected function codesForGetAsParameter(): array;

    abstract protected function codesForGetAsJson(): array;

    protected function getUri(): string
    {
        return '/api/login';
    }

    protected function getClient()
    {
        $app = $this->createApplication($this->getOptions());
        $client = parent::createClient($app);

        return $client;
    }

    protected function getCredentials($type = null)
    {
        switch ($type) {
            case 'right':
                return ['username' => 'admin', 'password' => 'foo'];
            case 'wrong password':
                return ['username' => 'admin', 'password' => 'bar'];
            case 'wrong username':
                return ['username' => 'not_admin', 'password' => 'foo'];
            case 'wrong trim':
                return ['username' => 'admin', 'password' => 'foo '];
            case 'no':
                return [];
            default:
                return null;
        }
    }

    protected function tryCredentials(Callable $callback, array $credential_codes = [])
    {
        foreach ($credential_codes as $credential => $code) {
            $result = $callback($credential);
            $this->assertTrue($code === $result, sprintf('Expected: %s, got: %s', $code, $result));
        }
    }

    protected function getCode($method, array $parameters = [], array $server = array(), $content = null)
    {
        $client = $this->getClient();
        $crawler = $client->request($method, $this->getUri(), $parameters, [], $server, $content);
        $response = $client->getResponse();
        $code = $response->getStatusCode();

        return $code;
    }

    public function testPostCredentialsAsForm()
    {
        $this->tryCredentials(function ($credential) {
            return $this->getCode('POST', $this->getCredentials($credential));
        }, $this->codesForPostAsForm());
    }

    public function testPostCredentialsAsJson()
    {
        $this->tryCredentials(function ($credential) {
            $content = json_encode($this->getCredentials($credential));
            return $this->getCode('POST', [], ['CONTENT_TYPE' => 'application/json'], $content);
        }, $this->codesForPostAsJson());
    }

    public function testGetCredentialsAsParameter()
    {
        $this->tryCredentials(function ($credential) {
            return $this->getCode('GET', $this->getCredentials($credential));
        }, $this->codesForGetAsParameter());
    }

    public function testGetCredentialsAsJson()
    {
        $this->tryCredentials(function ($credential) {
            $content = json_encode($this->getCredentials($credential));
            return $this->getCode('GET', [], ['CONTENT_TYPE' => 'application/json'], $content);
        }, $this->codesForGetAsJson());
    }
}