<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Bridge\Spl;

use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\Security\SensitiveValue;
use PHPUnit\Framework\TestCase;

class ArrayObjectTest extends TestCase
{
    public function testShouldBeSubClassOfArrayObject(): void
    {
        $rc = new \ReflectionClass(ArrayObject::class);

        static::assertTrue($rc->isSubclassOf(\ArrayObject::class));
    }

    public function testShouldAllowGetPreviouslySetValueByIndex(): void
    {
        $array = new ArrayObject();
        $array['foo'] = 'bar';

        static::assertArrayHasKey('foo', $array);
        static::assertSame('bar', $array['foo']);
    }

    public function testShouldAllowGetValueSetInInternalArrayObject(): void
    {
        $internalArray = new \ArrayObject();
        $internalArray['foo'] = 'bar';

        $array = new ArrayObject($internalArray);

        static::assertArrayHasKey('foo', $array);
        static::assertSame('bar', $array['foo']);
    }

    public function testShouldAllowGetNullIfValueWithIndexNotSet(): void
    {
        $array = new ArrayObject();

        static::assertArrayNotHasKey('foo', $array);
        static::assertNull($array['foo']);
    }

    public function testShouldReplaceFromArray(): void
    {
        $expectedArray = [
            'foo' => 'valNew',
            'ololo' => 'valCurr',
            'baz' => 'bazNew',
        ];

        $array = new ArrayObject([
            'foo' => 'valCurr',
            'ololo' => 'valCurr',
        ]);

        $array->replace([
            'foo' => 'valNew',
            'baz' => 'bazNew',
        ]);

        static::assertSame($expectedArray, (array) $array);
    }

    public function testShouldReplaceFromTraversable(): void
    {
        $traversable = new \ArrayIterator([
            'foo' => 'valNew',
            'baz' => 'bazNew',
        ]);

        $expectedArray = [
            'foo' => 'valNew',
            'ololo' => 'valCurr',
            'baz' => 'bazNew',
        ];

        $array = new ArrayObject([
            'foo' => 'valCurr',
            'ololo' => 'valCurr',
        ]);

        $array->replace($traversable);

        static::assertSame($expectedArray, (array) $array);
    }

    public function testShouldAllowCastToArrayFromCustomArrayObject(): void
    {
        $input = new CustomArrayObject();
        $input['foo'] = 'barbaz';

        $arrayObject = new ArrayObject($input);

        $array = (array) $arrayObject;

        static::assertIsArray($array);
        static::assertSame([
            'foo' => 'barbaz',
        ], $array);
    }

    public function testShouldAllowSetToCustomArrayObject(): void
    {
        $input = new CustomArrayObject();
        $input['foo'] = 'barbaz';

        $arrayObject = new ArrayObject($input);
        $arrayObject['foo'] = 'ololo';

        static::assertSame('ololo', $input['foo']);
    }

    public function testShouldAllowUnsetToCustomArrayObject(): void
    {
        $input = new CustomArrayObject();
        $input['foo'] = 'barbaz';

        $arrayObject = new ArrayObject($input);
        unset($arrayObject['foo']);

        static::assertNull($input['foo']);
    }

    public function testShouldAllowGetValueFromCustomArrayObject(): void
    {
        $input = new CustomArrayObject();
        $input['foo'] = 'barbaz';

        $arrayObject = new ArrayObject($input);

        static::assertSame('barbaz', $arrayObject['foo']);
    }

    public function testShouldAllowIssetValueFromCustomArrayObject(): void
    {
        $input = new CustomArrayObject();
        $input['foo'] = 'barbaz';

        $arrayObject = new ArrayObject($input);

        static::assertArrayHasKey('foo', $arrayObject);
        static::assertArrayNotHasKey('bar', $arrayObject);
    }

    public function testShouldAllowIterateOverCustomArrayObject(): void
    {
        $input = new CustomArrayObject();
        $input['foo'] = 'barbaz';

        $arrayObject = new ArrayObject($input);

        $array = iterator_to_array($arrayObject);

        static::assertIsArray($array);
        static::assertSame([
            'foo' => 'barbaz',
        ], $array);
    }

    public function testShouldReturnFalseIfRequiredFieldEmptyAndThrowOnInvalidFalse(): void
    {
        $arrayObject = new ArrayObject();

        static::assertFalse($arrayObject->validateNotEmpty(['aRequiredField'], $throwOnInvalid = false));
    }

    public function testShouldAllowValidateScalarWhetherItNotEmpty(): void
    {
        $arrayObject = new ArrayObject();

        static::assertFalse($arrayObject->validateNotEmpty('aRequiredField', $throwOnInvalid = false));
    }

    public function testShouldReturnTrueIfRequiredFieldsNotEmpty(): void
    {
        $arrayObject = new ArrayObject();
        $arrayObject['aRequiredField'] = 'foo';
        $arrayObject['otherRequiredField'] = 'bar';

        static::assertTrue($arrayObject->validateNotEmpty(['aRequiredField', 'otherRequiredField']));
    }

    public function testShouldReturnFalseIfRequiredFieldNotSetAndThrowOnInvalidFalse(): void
    {
        $arrayObject = new ArrayObject();

        static::assertFalse($arrayObject->validatedKeysSet(['aRequiredField'], $throwOnInvalid = false));
    }

    public function testShouldAllowValidateScalarNotSet(): void
    {
        $arrayObject = new ArrayObject();

        static::assertFalse($arrayObject->validatedKeysSet('aRequiredField', $throwOnInvalid = false));
    }

    public function testShouldReturnTrueIfRequiredFieldsSet(): void
    {
        $arrayObject = new ArrayObject();
        $arrayObject['aRequiredField'] = 'foo';
        $arrayObject['otherRequiredField'] = 'bar';

        static::assertTrue($arrayObject->validatedKeysSet(['aRequiredField', 'otherRequiredField']));
    }

    public function testShouldConvertArrayObjectToPrimitiveArrayMakingSensitiveValueUnsafeAndEraseIt(): void
    {
        $sensitiveValue = new SensitiveValue('theCreditCard');

        $arrayObject = new ArrayObject();
        $arrayObject['creditCard'] = $sensitiveValue;
        $arrayObject['email'] = 'bar@example.com';

        $primitiveArray = $arrayObject->toUnsafeArray();

        static::assertIsArray($primitiveArray);

        static::assertArrayHasKey('creditCard', $primitiveArray);
        static::assertSame('theCreditCard', $primitiveArray['creditCard']);

        static::assertArrayHasKey('email', $primitiveArray);
        static::assertSame('bar@example.com', $primitiveArray['email']);

        static::assertNull($sensitiveValue->peek());
    }

    public function testShouldAllowSetDefaultValues(): void
    {
        $arrayObject = new ArrayObject();
        $arrayObject['foo'] = 'fooVal';

        $arrayObject->defaults([
            'foo' => 'fooDefVal',
            'bar' => 'barDefVal',
        ]);

        static::assertSame('fooVal', $arrayObject['foo']);
        static::assertSame('barDefVal', $arrayObject['bar']);
    }

    public function shouldAllowGetArrayAsArrayObjectIfSet(): void
    {
        $array = new ArrayObject();
        $array['foo'] = [
            'foo' => 'fooVal',
        ];

        $subArray = $array->getArray('foo');

        static::assertSame([
            'foo' => 'fooVal',
        ], (array) $subArray);
    }

    public function shouldAllowGetArrayAsArrayObjectIfNotSet(): void
    {
        $array = new ArrayObject();

        $subArray = $array->getArray('foo');

        static::assertSame([], (array) $subArray);
    }

    public function shouldAllowToArrayWithoutSensitiveValuesAdnLocal(): void
    {
        $array = new ArrayObject([
            'local' => 'theLocal',
            'sensitive' => new SensitiveValue('theSens'),
            'foo' => 'fooVal',
        ]);

        static::assertSame([
            'foo' => 'fooVal',
        ], $array->toUnsafeArrayWithoutLocal());
    }
}

class CustomArrayObject implements \ArrayAccess, \IteratorAggregate
{
    private mixed $foo;

    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset): bool
    {
        return $offset === 'foo';
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset): void
    {
        $this->{$offset} = null;
    }

    #[\ReturnTypeWillChange]
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator([
            'foo' => $this->foo,
        ]);
    }
}
