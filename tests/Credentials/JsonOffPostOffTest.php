<?php

require_once __DIR__ . '/AbstractCredentialsTestCase.php';

class JsonOffPostOffTest extends AbstractCredentialsTestCase
{
    protected function getOptions()
    {
        return ['json_only' => false, 'post_only' => false];
    }

    protected function codesForPostAsForm(): array
    {
        return [
            'no' => 401,
            'right' => 200,
            'wrong password' => 401,
            'wrong username' => 401,
            'wrong trim' => 401
        ];
    }

    protected function codesForPostAsJson(): array
    {
        return [
            'no' => 401,
            'right' => 200,
            'wrong password' => 401,
            'wrong username' => 401,
            'wrong trim' => 401
        ];
    }

    protected function codesForGetAsParameter(): array
    {
        return [
            'no' => 401,
            'right' => 200,
            'wrong password' => 401,
            'wrong username' => 401,
            'wrong trim' => 401
        ];
    }

    protected function codesForGetAsJson(): array
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