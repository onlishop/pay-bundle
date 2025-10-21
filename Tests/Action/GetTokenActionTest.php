<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Action;

use Onlishop\Bundle\PayBundle\Action\GetTokenAction;
use Onlishop\Bundle\PayBundle\Model\Identity;
use Onlishop\Bundle\PayBundle\Request\GetToken;
use Onlishop\Bundle\PayBundle\Security\TokenInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class GetTokenActionTest extends TestCase
{
    public function testShouldSetFoundToken(): void
    {
        $hash = 'theHash';
        $token = $this->createMock(TokenInterface::class);

        $tokenStorage = $this->createMock(StorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('find')
            ->with(new Identity($hash, TokenInterface::class))
            ->willReturn($token)
        ;

        $action = new GetTokenAction($tokenStorage);

        $request = new GetToken($hash);

        $action->execute($request);

        static::assertSame($token, $request->getToken());
    }
}
