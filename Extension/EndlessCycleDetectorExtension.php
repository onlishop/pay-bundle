<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Extension;

use Onlishop\Bundle\PayBundle\PayException;

class EndlessCycleDetectorExtension implements ExtensionInterface
{
    public function __construct(protected int $limit = 100)
    {
    }

    public function onPreExecute(Context $context): void
    {
        if (\count($context->getPrevious()) >= $this->limit) {
            throw PayException::endlessCycleDetected($this->limit);
        }
    }

    public function onExecute(Context $context): void
    {
    }

    public function onPostExecute(Context $context): void
    {
    }
}
