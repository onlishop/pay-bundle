<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Request;

use Onlishop\Bundle\PayBundle\Model\ModelAggregateInterface;
use Onlishop\Bundle\PayBundle\Model\ModelAwareInterface;
use Onlishop\Bundle\PayBundle\Security\TokenInterface;
use Onlishop\Bundle\PayBundle\Storage\IdentityInterface;

abstract class Generic implements ModelAggregateInterface, ModelAwareInterface
{
    protected mixed $model;

    protected mixed $firstModel = null;

    protected ?TokenInterface $token = null;

    public function __construct(
        mixed $model,
    ) {
        $this->setModel($model);
        if ($model instanceof TokenInterface) {
            $this->token = $model;
        }
    }

    public function getModel(): mixed
    {
        return $this->model;
    }

    public function setModel(mixed $model): void
    {
        $this->model = $model;
        $this->setFirstModel($model);
    }

    protected function setFirstModel(mixed $model): void
    {
        if ($this->firstModel) {
            return;
        }
        if ($model instanceof TokenInterface) {
            return;
        }
        if ($model instanceof IdentityInterface) {
            return;
        }

        $this->firstModel = $model;
    }
}
