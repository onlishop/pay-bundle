<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Security;

use League\Uri\Components\HierarchicalPath;
use League\Uri\Components\Path;
use League\Uri\Http as HttpUri;
use League\Uri\Modifier;
use Onlishop\Bundle\PayBundle\Registry\StorageRegistryInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

class TokenFactory extends AbstractTokenFactory
{
    protected HttpUri $baseUrl;

    /**
     * @param StorageInterface<TokenInterface> $tokenStorage
     */
    public function __construct(StorageInterface $tokenStorage, StorageRegistryInterface $storageRegistry, ?string $baseUrl = null)
    {
        parent::__construct($tokenStorage, $storageRegistry);

        $this->baseUrl = $baseUrl ? HttpUri::new($baseUrl) : HttpUri::fromServer($_SERVER);
    }

    protected function generateUrl(string $path, array $parameters = []): string
    {
        $hierarchicalPath = HierarchicalPath::fromUri($this->baseUrl);
        if (pathinfo($hierarchicalPath->getBasename(), \PATHINFO_EXTENSION) === 'php') {
            $newPath = (new Modifier($this->baseUrl))->replaceBasename(Path::new($path)->withoutLeadingSlash());
        } else {
            $newPath = (new Modifier($this->baseUrl))->appendSegment($path)->getUriString();
        }

        $uri = $this->baseUrl->withPath($newPath);
        $uri = $this->addQueryToUri($uri, $parameters);

        return (string) $uri;
    }
}
