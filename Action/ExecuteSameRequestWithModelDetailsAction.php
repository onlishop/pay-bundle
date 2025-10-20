<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Action;

use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\GatewayAwareInterface;
use Onlishop\Bundle\PayBundle\GatewayAwareTrait;
use Onlishop\Bundle\PayBundle\Model\DetailsAggregateInterface;
use Onlishop\Bundle\PayBundle\Model\DetailsAwareInterface;
use Onlishop\Bundle\PayBundle\Model\ModelAggregateInterface;
use Onlishop\Bundle\PayBundle\Model\ModelAwareInterface;

class ExecuteSameRequestWithModelDetailsAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param ModelAggregateInterface&ModelAwareInterface $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var DetailsAggregateInterface $model */
        $model = $request->getModel();
        $details = $model->getDetails();

        $request->setModel($details);
        try {
            $this->gateway->execute($request);
        } finally {
            if ($model instanceof DetailsAwareInterface) {
                $model->setDetails($details);
            }
        }
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof ModelAggregateInterface
            && $request instanceof ModelAwareInterface
            && $request->getModel() instanceof DetailsAggregateInterface;
    }
}
