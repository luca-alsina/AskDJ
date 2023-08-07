<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class BaseController
{

    protected function json(
        array $data,
        int|null $status    = null,
        array $headers      = []
    ): Response
    {

        $status ??= 200;

        return new Response(
            content:    json_encode($data),
            status:     $status,
            headers:    [
                'Content-Type' => 'application/json',
                ...$headers
            ]
        );
    }

}