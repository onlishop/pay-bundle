<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Extension;

interface ExtensionInterface
{
    public function onPreExecute(Context $context): void;

    public function onExecute(Context $context): void;

    public function onPostExecute(Context $context): void;
}
