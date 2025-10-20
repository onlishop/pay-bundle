<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Exception;

use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Debug\Humanify;

class RequestNotSupportedException extends InvalidArgumentException
{
    protected mixed $request;

    protected ?ActionInterface $action;

    public function getRequest(): mixed
    {
        return $this->request;
    }

    public function setRequest(mixed $request): void
    {
        $this->request = $request;
    }

    public function getAction(): ?ActionInterface
    {
        return $this->action;
    }

    public function setAction(?ActionInterface $action): void
    {
        $this->action = $action;
    }

    public static function create(mixed $request): RequestNotSupportedException
    {
        $exception = new self(\sprintf(
            'Request %s is not supported.',
            Humanify::request($request),
        ));

        $exception->request = $request;

        return $exception;
    }

    public static function assertSupports(ActionInterface $action, $request): void
    {
        if (!$action->supports($request)) {
            throw static::createActionNotSupported($action, $request);
        }
    }

    public static function createActionNotSupported(ActionInterface $action, $request): RequestNotSupportedException
    {
        $exception = new self(\sprintf(
            'Action %s is not supported the request %s.',
            Humanify::value($action),
            Humanify::request($request),
        ));

        $exception->request = $request;
        $exception->action = $action;

        return $exception;
    }
}
