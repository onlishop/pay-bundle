<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Request;

use Onlishop\Bundle\PayBundle\Security\TokenInterface;

class GetToken
{
    private ?TokenInterface $token = null;

    public function __construct(
        protected readonly string $hash
    ) {
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getToken(): ?TokenInterface
    {
        return $this->token;
    }

    public function setToken(TokenInterface $token): void
    {
        $this->token = $token;
    }
}
