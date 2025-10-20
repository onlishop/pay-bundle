<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Bundle\PayBundle\Reply\ReplyInterface;

interface GatewayInterface
{
    public function execute(mixed $request, bool $catchReply = false): ?ReplyInterface;
}
