<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Storage;


use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Model\Identity;

/**
 * @template T of object
 *
 * @extends AbstractStorage<T>
 */
class FilesystemStorage extends AbstractStorage
{
    protected string $storageDir;

    protected string $idProperty;

    protected array $identityMap;

    public function __construct(string $storageDir, string $modelClass, string $idProperty = 'pay_id')
    {
        parent::__construct($modelClass);

        $this->storageDir = $storageDir;
        $this->idProperty = $idProperty;
    }

    public function findBy(array $criteria): array
    {
        throw new LogicException('Method is not supported by the storage.');
    }

    protected function doFind(mixed $id): ?object
    {
        if (isset($this->identityMap[$id])) {
            return $this->identityMap[$id];
        }

        if (file_exists($this->storageDir . '/pay-model-' . $id)) {
            return $this->identityMap[$id] = unserialize(file_get_contents($this->storageDir . '/pay-model-' . $id));
        }

        return null;
    }

    protected function doUpdateModel(object $model): object
    {
        $ro = new \ReflectionObject($model);

        if (!$ro->hasProperty($this->idProperty)) {
            $model->{$this->idProperty} = null;
        }

        $rp = $ro->getProperty($this->idProperty);

        $id = $rp->isInitialized($model) ? $rp->getValue($model) : null;

        if (empty($id)) {
            $id = uniqid('', true);
            $rp->setValue($model, $id);
        }

        $this->identityMap[$id] = $model;

        file_put_contents(
            $this->storageDir . '/pay-model-' . $id,
            serialize($model)
        );

        return $model;
    }

    protected function doDeleteModel(object $model): void
    {
        $rp = new \ReflectionProperty($model, $this->idProperty);

        $id = $rp->isInitialized($model) ? $rp->getValue($model) : null;

        if (!empty($id)) {
            $file = $this->storageDir . '/pay-model-' . $id;

            if (is_file($file)) {
                @unlink($file);
            }

            unset($this->identityMap[$id]);
        }
    }

    protected function doGetIdentity(object $model): Identity
    {
        $rp = new \ReflectionProperty($model, $this->idProperty);

        if (!$rp->isInitialized($model)) {
            throw new \LogicException(\sprintf(
                'Property "%s" is not initialized on model "%s".',
                $this->idProperty,
                get_debug_type($model)
            ));
        }

        $id = $rp->getValue($model);

        if (empty($id)) {
            throw new LogicException('The model must be persisted before usage of this method.');
        }

        return new Identity($id, $model);
    }
}
