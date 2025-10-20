<?php declare(strict_types=1);

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onlishop\Bundle\PayBundle\Security\Util;

/**
 * This is adopted version ot TokenGenerator class from FOSUserBundle
 *
 * @see https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Util/TokenGenerator.php
 */
class Random
{
    public static function generateToken(): string
    {
        return rtrim(strtr(base64_encode((string) self::getRandomNumber()), '+/', '-_'), '=');
    }

    private static function getRandomNumber(): string
    {
        return random_bytes(32);
    }
}
