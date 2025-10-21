<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Action;


use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Model\Identity;
use Onlishop\Bundle\PayBundle\Request\GetToken;
use Onlishop\Bundle\PayBundle\Security\TokenInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

class GetTokenAction implements ActionInterface
{
    /**
     * @param StorageInterface<TokenInterface> $tokenStorage
     */
    public function __construct(
        protected readonly StorageInterface $tokenStorage,
    ) {
    }

    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        if (!$token = $this->tokenStorage->find(new Identity($request->getHash(), TokenInterface::class))) {
            throw new LogicException(\sprintf('The token %s could not be found', $request->getHash()));
        }

        $request->setToken($token);
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof GetToken;
    }
}
