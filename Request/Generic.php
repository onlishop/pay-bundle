<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Request;

use Onlishop\Bundle\PayBundle\Model\ModelAggregateInterface;
use Onlishop\Bundle\PayBundle\Model\ModelAwareInterface;
use Onlishop\Bundle\PayBundle\Security\TokenInterface;
use Onlishop\Bundle\PayBundle\Storage\IdentityInterface;

abstract class Generic implements ModelAggregateInterface, ModelAwareInterface
{
    protected mixed $firstModel = null;

    protected ?TokenInterface $token = null;

    public function __construct(
        protected mixed $model,
    ) {
        if ($model instanceof TokenInterface) {
            $this->token = $model;
        }
    }

    public function getModel(): mixed
    {
        return $this->model;
    }

    public function getToken(): ?TokenInterface
    {
        return $this->token;
    }

    public function setModel(mixed $model): void
    {
        if (\is_array($model)) {
            $model = new \ArrayObject($model);
        }

        $this->model = $model;

        $this->setFirstModel($model);
    }

    protected function setFirstModel(mixed $model): void
    {
        if ($this->firstModel) {
            return;
        }

        if ($model instanceof IdentityInterface) {
            return;
        }

        $this->firstModel = $model;
    }
}
