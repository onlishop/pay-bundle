<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Reply;

use Symfony\Component\HttpFoundation\Response;

class HttpResponse extends Base
{
    public function __construct(protected Response $response)
    {
        parent::__construct();
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
