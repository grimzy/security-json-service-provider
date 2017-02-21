<?php
namespace Grimzy\SecurityJsonServiceProvider\Tests\Credentials;

class JsonOffPostOffTest extends AbstractCredentialsTestCase
{
    protected function getOptions()
    {
        return ['json_only' => false, 'post_only' => false];
    }

    protected function codesForPostAsForm()
    {
        return [
            'no' => 401,
            'right' => 200,
            'wrong password' => 401,
            'wrong username' => 401,
            'wrong trim' => 401
        ];
    }

    protected function codesForPostAsJson()
    {
        return [
            'no' => 401,
            'right' => 200,
            'wrong password' => 401,
            'wrong username' => 401,
            'wrong trim' => 401
        ];
    }

    protected function codesForGetAsParameter()
    {
        return [
            'no' => 401,
            'right' => 200,
            'wrong password' => 401,
            'wrong username' => 401,
            'wrong trim' => 401
        ];
    }

    protected function codesForGetAsJson()
    {
        return [
            'no' => 401,
            'right' => 200,
            'wrong password' => 401,
            'wrong username' => 401,
            'wrong trim' => 401
        ];
    }
}