<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Security;

use Onlishop\Bundle\PayBundle\Registry\StorageRegistryInterface;
use Onlishop\Bundle\PayBundle\Security\TokenFactoryInterface;
use Onlishop\Bundle\PayBundle\Security\Util\Random;
use League\Uri\Components\Query;
use League\Uri\Http as HttpUri;
use Onlishop\Bundle\PayBundle\Storage\IdentityInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

abstract class AbstractTokenFactory implements TokenFactoryInterface
{
    /**
     * @var StorageInterface<TokenInterface>
     */
    protected StorageInterface $tokenStorage;

    /**
     * @var StorageRegistryInterface<StorageInterface<TokenInterface>>
     */
    protected StorageRegistryInterface $storageRegistry;

    /**
     * @param StorageInterface<TokenInterface> $tokenStorage
     * @param StorageRegistryInterface<StorageInterface<TokenInterface>> $storageRegistry
     */
    public function __construct(StorageInterface $tokenStorage, StorageRegistryInterface $storageRegistry)
    {
        $this->tokenStorage = $tokenStorage;
        $this->storageRegistry = $storageRegistry;
    }

    public function createToken($gatewayName, $model, $targetPath, array $targetParameters = [], $afterPath = null, array $afterParameters = []): TokenInterface
    {
        /** @var TokenInterface $token */
        $token = $this->tokenStorage->create();
        $token->setHash($token->getHash() ?: Random::generateToken());

        $targetParameters = array_replace([
            'pay_token' => $token->getHash(),
        ], $targetParameters);

        $token->setGatewayName($gatewayName);

        if ($model instanceof IdentityInterface) {
            $token->setDetails($model);
        } elseif ($model !== null) {
            $token->setDetails($this->storageRegistry->getStorage($model)->identify($model));
        }

        if (str_starts_with($targetPath, 'http')) {
            $targetUri = HttpUri::new($targetPath);
            $targetUri = $this->addQueryToUri($targetUri, $targetParameters);

            $token->setTargetUrl((string) $targetUri);
        } else {
            $token->setTargetUrl($this->generateUrl($targetPath, $targetParameters));
        }

        if ($afterPath && str_starts_with($afterPath, 'http')) {
            $afterUri = HttpUri::new($afterPath);
            $afterUri = $this->addQueryToUri($afterUri, $afterParameters);

            $token->setAfterUrl((string) $afterUri);
        } elseif ($afterPath) {
            $token->setAfterUrl($this->generateUrl($afterPath, $afterParameters));
        }

        $this->tokenStorage->update($token);

        return $token;
    }

    protected function addQueryToUri(HttpUri $uri, array $query): HttpUri
    {
        $uriQuery = Query::fromUri($uri)->withoutEmptyPairs();

        $query = array_replace($uriQuery->parameters(), $query);

        return $uri->withQuery((string) Query::fromVariable($query));
    }

    abstract protected function generateUrl(string $path, array $parameters = []): string;
}
