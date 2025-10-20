<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Request;

abstract class BaseGetStatus extends Generic implements GetStatusInterface
{
    protected mixed $status;

    public function __construct(mixed $model)
    {
        parent::__construct($model);

        $this->markUnknown();
    }

    public function getValue(): mixed
    {
        return $this->status;
    }
}
