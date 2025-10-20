<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Action;

interface ActionInterface
{
    public function execute(mixed $request): void;

    public function supports(mixed $request): bool;
}
