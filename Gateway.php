<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Extension\Context;
use Onlishop\Bundle\PayBundle\Extension\ExtensionCollection;
use Onlishop\Bundle\PayBundle\Extension\ExtensionInterface;
use Onlishop\Bundle\PayBundle\Reply\ReplyInterface;

class Gateway implements GatewayInterface
{
    /**
     * @var list<class-string<ActionInterface>|ActionInterface>
     */
    protected array $actions = [];

    protected ExtensionCollection $extensions;

    /**
     * @var Context[]
     */
    protected array $stack = [];

    public function __construct()
    {
        $this->extensions = new ExtensionCollection();
    }

    public function execute(mixed $request, bool $catchReply = false): ?ReplyInterface
    {
        $context = new Context($this, $request, $this->stack);

        $this->stack[] = $context;

        try {
            $this->extensions->onPreExecute($context);

            if (!$context->getAction()) {
                if (!$action = $this->findActionSupported($context->getRequest())) {
                    throw RequestNotSupportedException::create($context->getRequest());
                }

                $context->setAction($action);
            }

            $this->extensions->onExecute($context);

            if ($context->getAction()) {
                $context->getAction()->execute($request);
            }

            $this->extensions->onPostExecute($context);

            array_pop($this->stack);
        } catch (ReplyInterface $reply) {
            $context->setReply($reply);

            $this->extensions->onPostExecute($context);
            array_pop($this->stack);

            if ($catchReply && $context->getReply()) {
                return $context->getReply();
            }
            if ($context->getReply()) {
                throw $context->getReply();
            }
        } catch (\Exception $e) {
            $context->setException($e);

            $this->onPostExecuteWithException($context);
        }

        return null;
    }

    public function addAction(ActionInterface $action, bool $forcePrepend = false): void
    {
        $forcePrepend ?
            array_unshift($this->actions, $action) :
            array_push($this->actions, $action);
    }

    public function addExtension(ExtensionInterface $extension, bool $forcePrepend = false): void
    {
        $this->extensions->addExtension($extension, $forcePrepend);
    }

    protected function onPostExecuteWithException(Context $context): void
    {
        array_pop($this->stack);

        $exception = $context->getException();

        try {
            $this->extensions->onPostExecute($context);
        } catch (\Exception $e) {
            $wrapper = $e;
            while (($prev = $wrapper->getPrevious()) instanceof \Throwable) {
                if ($exception === $wrapper = $prev) {
                    throw $e;
                }
            }

            $prev = new \ReflectionProperty($wrapper, 'previous');
            $prev->setValue($wrapper, $exception);

            throw $e;
        }

        if ($context->getException()) {
            throw $context->getException();
        }
    }

    protected function findActionSupported(mixed $request): ?ActionInterface
    {
        foreach ($this->actions as $action) {
            if ($action instanceof GatewayAwareInterface) {
                $action->setGateway($this);
            }
            if ($action instanceof ActionInterface) {
                if ($action->supports($request)) {
                    return $action;
                }
            }
        }

        return null;
    }
}
