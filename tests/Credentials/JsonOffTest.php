<?php

class JsonOffTest extends AbstractCredentialsTestCase
{
    protected function getOptions()
    {
        return ['json_only' => false, 'post_only' => true];
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
            'no' => 405,
            'right' => 405,
            'wrong password' => 405,
            'wrong username' => 405,
            'wrong trim' => 405
        ];
    }

    protected function codesForGetAsJson(): array
    {
        return [
            'no' => 405,
            'right' => 405,
            'wrong password' => 405,
            'wrong username' => 405,
            'wrong trim' => 405
        ];
    }
}