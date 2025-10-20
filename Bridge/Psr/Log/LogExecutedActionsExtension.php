<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Bridge\Psr\Log;

use Onlishop\Bundle\PayBundle\Debug\Humanify;
use Onlishop\Bundle\PayBundle\Extension\Context;
use Onlishop\Bundle\PayBundle\Extension\ExtensionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class LogExecutedActionsExtension implements ExtensionInterface, LoggerAwareInterface
{
    public function __construct(
        protected ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?: new NullLogger();
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
        $this->logger->debug(\sprintf(
            '[Pay] %d# %s::execute(%s)',
            \count($context->getPrevious()) + 1,
            Humanify::value($context->getAction(), false),
            Humanify::request($context->getRequest())
        ));
    }

    public function onPostExecute(Context $context): void
    {
        if ($context->getReply()) {
            $this->logger->debug(\sprintf(
                '[Pay] %d# %s::execute(%s) throws reply %s',
                \count($context->getPrevious()) + 1,
                Humanify::value($context->getAction()),
                Humanify::request($context->getRequest()),
                Humanify::request($context->getReply())
            ));
        } elseif ($context->getException()) {
            $this->logger->debug(\sprintf(
                '[Pay] %d# %s::execute(%s) throws exception %s',
                \count($context->getPrevious()) + 1,
                $context->getAction() ? Humanify::value($context->getAction()) : 'Gateway',
                Humanify::request($context->getRequest()),
                Humanify::value($context->getException())
            ));
        }
    }
}
