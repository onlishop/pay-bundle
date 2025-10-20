<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Extension;

use Onlishop\Bundle\PayBundle\Event\ExecuteEvent;
use Onlishop\Bundle\PayBundle\PayEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcherExtension implements ExtensionInterface
{
    public function __construct(protected readonly EventDispatcherInterface $dispatcher)
    {
    }

    public function onPreExecute(Context $context): void
    {
        $event = new ExecuteEvent($context);
        $this->dispatcher->dispatch($event, PayEvents::GATEWAY_PRE_EXECUTE);
    }

    public function onExecute(Context $context): void
    {
        $event = new ExecuteEvent($context);
        $this->dispatcher->dispatch($event, PayEvents::GATEWAY_EXECUTE);
    }

    public function onPostExecute(Context $context): void
    {
        $event = new ExecuteEvent($context);
        $this->dispatcher->dispatch($event, PayEvents::GATEWAY_POST_EXECUTE);
    }
}
