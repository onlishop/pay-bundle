<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Security;

use Onlishop\Bundle\PayBundle\Registry\StorageRegistryInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TokenFactory extends AbstractTokenFactory
{
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(StorageInterface $tokenStorage, StorageRegistryInterface $storageRegistry, UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;

        parent::__construct($tokenStorage, $storageRegistry);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function generateUrl($path, array $parameters = []): string
    {
        return $this->urlGenerator->generate($path, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
