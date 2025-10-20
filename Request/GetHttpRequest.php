<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Request;

class GetHttpRequest
{
    public array $query = [];

    public array $request = [];

    public string $method = '';

    public string $uri = '';

    public string $clientIp = '';

    public string $userAgent = '';

    public string $content = '';

    public array $headers = [];

    public function __construct()
    {
    }
}
