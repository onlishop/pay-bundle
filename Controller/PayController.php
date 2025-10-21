<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Controller;

use Onlishop\Bundle\PayBundle\Pay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class PayController extends AbstractController
{
    public function __construct(protected readonly Pay $pay)
    {
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'pay' => Pay::class,
        ]);
    }
}
