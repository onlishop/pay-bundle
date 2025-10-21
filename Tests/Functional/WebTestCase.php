<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Functional;

use Onlishop\Bundle\PayBundle\Tests\Functional\app\AppKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        restore_exception_handler();
    }

    public static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
