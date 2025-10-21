<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Model;

interface DetailsAwareInterface
{
    public function setDetails(iterable $details): void;
}
