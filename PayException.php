<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Core\Framework\HttpException;
use Symfony\Component\HttpFoundation\Response;

class PayException extends HttpException
{
    public const REQUEST_NOT_SUPPORTED = 'FRAMEWORK__PAY_REQUEST_NOT_SUPPORTED';
    public const ACTION_NOT_SUPPORTED = 'FRAMEWORK__PAY_ACTION_NOT_SUPPORTED';
    public const GATEWAY_NOT_FOUND = 'FRAMEWORK__PAY_GATEWAY_NOT_FOUND';
    public const GATEWAY_FACTORY_NOT_FOUND = 'FRAMEWORK__PAY_GATEWAY_FACTORY_NOT_FOUND';

    final public const EXECUTION_ENDLESS_CYCLE_DETECTED = 'EXECUTION__ENDLESS_CYCLE_DETECTED';
    private const INVALID_ARGUMENT = 'FRAMEWORK__PAY_INVALID_ARGUMENT';
    private const LOGIC_EXCEPTION = 'FRAMEWORK__PAY_LOGIC_EXCEPTION';

    public static function logicException(string $message): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::LOGIC_EXCEPTION,
            $message
        );
    }

    public static function invalidArgumentException(string $message): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::INVALID_ARGUMENT,
            $message
        );
    }

    public static function endlessCycleDetected(int $limit): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::EXECUTION_ENDLESS_CYCLE_DETECTED,
            'Possible endless cycle detected. ::onPreExecute was called {{ count }} times before reaching the limit.',
            ['count' => $limit],
        );
    }

    public static function gatewayFactoryNotFound(string $name): self
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            self::GATEWAY_FACTORY_NOT_FOUND,
            'Gateway factory "{{ name }}" does not exist.',
            ['name' => $name],
        );
    }

    public static function gatewayNotFound(string $name): self
    {
        return new self(
            Response::HTTP_NOT_FOUND,
            self::GATEWAY_NOT_FOUND,
            'Gateway "{{gateway}}" does not exist.',
            ['gateway' => $name],
        );
    }
}
