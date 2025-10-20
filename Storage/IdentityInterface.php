<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Storage;

interface IdentityInterface extends \Serializable
{
    public function getClass(): string;

    public function getId(): mixed;
}
