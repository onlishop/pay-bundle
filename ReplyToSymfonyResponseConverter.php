<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Reply\HttpResponse as SymfonyHttpResponse;
use Onlishop\Bundle\PayBundle\Reply\ReplyInterface;
use Symfony\Component\HttpFoundation\Response;

class ReplyToSymfonyResponseConverter
{
    public function convert(ReplyInterface $reply): Response
    {
        if ($reply instanceof SymfonyHttpResponse) {
            return $reply->getResponse();
        }

        $ro = new \ReflectionObject($reply);

        throw new LogicException(
            \sprintf('Cannot convert reply %s to http response.', $ro->getShortName()),
            0,
            $reply
        );
    }
}
