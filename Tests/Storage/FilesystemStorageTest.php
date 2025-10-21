<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Storage;

use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Model\Identity;
use Onlishop\Bundle\PayBundle\Storage\AbstractStorage;
use Onlishop\Bundle\PayBundle\Storage\FilesystemStorage;
use Onlishop\Bundle\PayBundle\Tests\fixtures\Model\TestModel;
use PHPUnit\Framework\TestCase;

class FilesystemStorageTest extends TestCase
{
    public function testShouldBeSubClassOfAbstractStorage(): void
    {
        $rc = new \ReflectionClass(FilesystemStorage::class);

        static::assertTrue($rc->isSubclassOf(AbstractStorage::class));
    }

    public function testShouldCreateFileWithModelInfoInStorageDirOnUpdateModel(): void
    {
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $model = $storage->create();
        $storage->update($model);

        static::assertFileExists(sys_get_temp_dir() . '/pay-model-' . $model->getId());
    }

    public function testShouldGenerateDifferentIdsForDifferentModels(): void
    {
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $modelOne = $storage->create();
        $storage->update($modelOne);

        $modelTwo = $storage->create();
        $storage->update($modelTwo);

        static::assertNotSame($modelOne->getId(), $modelTwo->getId());
    }

    public function testThrowIfTryGetIdentifierOfNotPersistedModel(): void
    {
        static::expectException(LogicException::class);
        $this->expectExceptionMessage('The model must be persisted before usage of this method');
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $model = $storage->create();

        // guard
        static::assertNull($model->getId());

        $storage->identify($model);
    }

    public function testShouldAllowGetModelIdentity(): void
    {
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $model = $storage->create();

        $storage->update($model);
        $firstId = $model->getId();

        $storage->update($model);
        $secondId = $model->getId();

        static::assertSame($firstId, $secondId);
    }

    public function testShouldAllowGetModelIdentityWhenDynamicIdUsed(): void
    {
        $storage = new FilesystemStorage(sys_get_temp_dir(), \stdClass::class);

        $model = $storage->create();

        $storage->update($model);

        $identity = $storage->identify($model);

        static::assertInstanceOf(Identity::class, $identity);
        static::assertSame(\stdClass::class, $identity->getClass());
        static::assertEquals($model->pay_id, $identity->getId());
    }

    public function testThrowIfTryToUseNotSupportedFindByMethod(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Method is not supported by the storage.');
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $storage->findBy([]);
    }

    public function testShouldFindModelById(): void
    {
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $model = $storage->create();
        $storage->update($model);

        // guard
        static::assertNotEmpty($model->getId());

        $foundModel = $storage->find($model->getId());

        static::assertInstanceOf(TestModel::class, $foundModel);
        static::assertEquals($model->getId(), $foundModel->getId());
    }

    public function testShouldFindModelByIdentity(): void
    {
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $model = $storage->create();
        $storage->update($model);

        // guard
        static::assertNotEmpty($model->getId());

        $identity = $storage->identify($model);

        // guard
        static::assertInstanceOf(Identity::class, $identity);

        $foundModel = $storage->find($identity);

        static::assertInstanceOf(TestModel::class, $foundModel);
        static::assertEquals($model->getId(), $foundModel->getId());
    }

    public function testShouldStoreInfoBetweenUpdateAndFind(): void
    {
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $model = $storage->create();
        $model->setPrice($expectedPrice = 123);
        $model->setCurrency($expectedCurrency = 'FOO');

        $storage->update($model);

        $foundModel = $storage->find($model->getId());

        static::assertSame($model, $foundModel);
        static::assertSame($expectedPrice, $foundModel->getPrice());
        static::assertSame($expectedCurrency, $foundModel->getCurrency());
    }

    public function testShouldStoreInfoBetweenUpdateAndFindWithDefaultId(): void
    {
        $storage = new FilesystemStorage(sys_get_temp_dir(), TestModel::class);

        $model = $storage->create();
        $model->setPrice($expectedPrice = 123);
        $model->setCurrency($expectedCurrency = 'FOO');

        $storage->update($model);

        // guard
        static::assertObjectHasProperty('pay_id', $model);
        static::assertNotEmpty($model->pay_id);

        $foundModel = $storage->find($model->pay_id);

        static::assertSame($model, $foundModel);
        static::assertSame($expectedPrice, $foundModel->getPrice());
        static::assertSame($expectedCurrency, $foundModel->getCurrency());

        static::assertObjectHasProperty('pay_id', $foundModel);
        static::assertNotEmpty($foundModel->pay_id);
    }

    public function testShouldAllowDeleteModel(): void
    {
        $storage = new FilesystemStorage(sys_get_temp_dir(), TestModel::class);

        $model = $storage->create();
        $model->setPrice($expectedPrice = 123);
        $model->setCurrency($expectedCurrency = 'FOO');

        $storage->update($model);

        // guard
        static::assertObjectHasProperty('pay_id', $model);
        static::assertNotEmpty($model->pay_id);

        $storage->delete($model);

        static::assertNull($storage->find($model->pay_id));
    }

    public function testShouldKeepIdTheSameOnSeveralUpdates(): void
    {
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            TestModel::class,
            'id'
        );

        $model = $storage->create();

        $storage->update($model);
        $firstId = $model->getId();

        $storage->update($model);
        $secondId = $model->getId();

        static::assertSame($firstId, $secondId);
    }

    public function testShouldUpdateModelAndSetIdToModelEvenIfModelNotHaveIdDefined(): void
    {
        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            \stdClass::class,
            'notExistProperty'
        );

        $model = $storage->create();

        $storage->update($model);

        static::assertInstanceOf(\stdClass::class, $model);
        static::assertObjectHasProperty('notExistProperty', $model);
    }

    public function testShouldCreateInstanceOfModelClassGivenInConstructor(): void
    {
        $expectedModelClass = TestModel::class;

        $storage = new FilesystemStorage(
            sys_get_temp_dir(),
            $expectedModelClass,
            'id'
        );

        $model = $storage->create();

        static::assertInstanceOf($expectedModelClass, $model);
        static::assertNull($model->getId());
    }
}
