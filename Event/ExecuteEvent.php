<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Event;

use Onlishop\Bundle\PayBundle\Extension\Context;
use Symfony\Contracts\EventDispatcher\Event;

class ExecuteEvent extends Event
{
    public function __construct(
        protected Context $context,
    ) {
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
