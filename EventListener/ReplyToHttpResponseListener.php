<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\EventListener;

use Onlishop\Bundle\PayBundle\Reply\ReplyInterface;
use Onlishop\Bundle\PayBundle\ReplyToSymfonyResponseConverter;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ReplyToHttpResponseListener
{
    private ReplyToSymfonyResponseConverter $replyToSymfonyResponseConverter;

    public function __construct(ReplyToSymfonyResponseConverter $replyToSymfonyResponseConverter)
    {
        $this->replyToSymfonyResponseConverter = $replyToSymfonyResponseConverter;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof ReplyInterface === false) {
            return;
        }

        /** @var $throwable ReplyInterface */
        $throwable = $event->getThrowable();
        $response = $this->replyToSymfonyResponseConverter->convert($throwable);

        $event->allowCustomResponseCode();

        $event->setResponse($response);
    }
}
