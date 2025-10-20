<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Security;

interface TokenFactoryInterface
{
    public function createToken(string $gatewayName, ?object $model, string $targetPath, array $targetParameters = [], ?string $afterPath = null, array $afterParameters = []): TokenInterface;
}
