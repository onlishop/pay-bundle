<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Security;

interface CryptedInterface
{
    public function decrypt(CypherInterface $cypher);

    public function encrypt(CypherInterface $cypher);
}
