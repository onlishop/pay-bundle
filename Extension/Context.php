<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Extension;

use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\GatewayInterface;
use Onlishop\Bundle\PayBundle\Reply\ReplyInterface;

class Context
{
    protected ?ActionInterface $action = null;

    protected ?\Exception $exception = null;

    protected ?ReplyInterface $reply = null;

    /**
     * @param Context[] $previous
     */
    public function __construct(
        protected readonly GatewayInterface $gateway,
        protected mixed $request,
        protected array $previous
    ) {
    }

    public function getAction(): ?ActionInterface
    {
        return $this->action;
    }

    public function setAction(?ActionInterface $action): void
    {
        $this->action = $action;
    }

    public function getException(): ?\Exception
    {
        return $this->exception;
    }

    public function setException(?\Exception $exception): void
    {
        $this->exception = $exception;
    }

    public function getReply(): ?ReplyInterface
    {
        return $this->reply;
    }

    public function setReply(?ReplyInterface $reply): void
    {
        $this->reply = $reply;
    }

    public function getGateway(): GatewayInterface
    {
        return $this->gateway;
    }

    public function getRequest(): mixed
    {
        return $this->request;
    }

    /**
     * @return Context[]
     */
    public function getPrevious(): array
    {
        return $this->previous;
    }
}
