<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Bridge\Psr\Log;

use Onlishop\Bundle\PayBundle\Extension\Context;
use Onlishop\Bundle\PayBundle\Extension\ExtensionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class LoggerExtension implements ExtensionInterface, LoggerAwareInterface
{
    protected NullLogger $nullLogger;

    public function __construct(
        protected ?LoggerInterface $logger = null,
    ) {
        $this->nullLogger = new NullLogger();
        $this->logger = $logger ?: $this->nullLogger;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function onPreExecute(Context $context): void
    {
    }

    public function onExecute(Context $context): void
    {
        $action = $context->getAction();
        if ($action instanceof LoggerAwareInterface) {
            $action->setLogger($this->logger);
        }
    }

    public function onPostExecute(Context $context): void
    {
        $action = $context->getAction();
        if ($action instanceof LoggerAwareInterface) {
            $action->setLogger($this->nullLogger);
        }
    }
}
