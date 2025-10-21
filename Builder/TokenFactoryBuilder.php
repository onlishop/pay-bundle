<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Builder;

use Onlishop\Bundle\PayBundle\Registry\StorageRegistryInterface;
use Onlishop\Bundle\PayBundle\Security\TokenFactory;
use Onlishop\Bundle\PayBundle\Security\TokenFactoryInterface;
use Onlishop\Bundle\PayBundle\Security\TokenInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TokenFactoryBuilder
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(): TokenFactoryInterface
    {
        return $this->build(...\func_get_args());
    }

    /**
     * @param StorageInterface<TokenInterface> $tokenStorage
     * @param StorageRegistryInterface<StorageInterface<TokenInterface>> $storageRegistry
     */
    public function build(StorageInterface $tokenStorage, StorageRegistryInterface $storageRegistry): TokenFactoryInterface
    {
        return new TokenFactory($tokenStorage, $storageRegistry, $this->urlGenerator);
    }
}
