<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Model;



use Onlishop\Bundle\PayBundle\Security\CryptedInterface;
use Onlishop\Bundle\PayBundle\Security\CypherInterface;

class GatewayConfig implements GatewayConfigInterface, CryptedInterface
{
    protected string $factoryName;

    protected string $gatewayName;

    protected array $config;

    protected array $decryptedConfig;

    public function __construct()
    {
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    public function setGatewayName(string $gatewayName): void
    {
        $this->gatewayName = $gatewayName;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
        $this->decryptedConfig = $config;
    }

    public function getConfig(): array
    {
        if (isset($this->config['encrypted'])) {
            return $this->decryptedConfig;
        }

        return $this->config;
    }

    public function decrypt(CypherInterface $cypher): void
    {
        if (empty($this->config['encrypted'])) {
            return;
        }

        foreach ($this->config as $name => $value) {
            if ($name === 'encrypted' || \is_bool($value)) {
                $this->decryptedConfig[$name] = $value;

                continue;
            }

            $this->decryptedConfig[$name] = $cypher->decrypt($value);
        }
    }

    public function encrypt(CypherInterface $cypher): void
    {
        $this->decryptedConfig['encrypted'] = true;

        foreach ($this->decryptedConfig as $name => $value) {
            if ($name === 'encrypted' || \is_bool($value)) {
                $this->config[$name] = $value;

                continue;
            }

            $this->config[$name] = $cypher->encrypt($value);
        }
    }
}
