<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Action;

use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Request\GetHttpRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class GetHttpRequestAction implements ActionInterface
{
    protected ?Request $httpRequest = null;

    protected ?RequestStack $httpRequestStack = null;

    /**
     * @deprecated
     */
    public function setHttpRequest(?Request $httpRequest = null): void
    {
        $this->httpRequest = $httpRequest;
    }

    public function setHttpRequestStack(?RequestStack $httpRequestStack = null): void
    {
        $this->httpRequestStack = $httpRequestStack;
    }

    public function execute($request): void
    {
        /** @var GetHttpRequest $request */
        RequestNotSupportedException::assertSupports($this, $request);

        $mainRequest = $this->httpRequestStack?->getMainRequest();
        if ($mainRequest !== null) {
            $this->updateRequest($request, $mainRequest);
        }
    }

    public function supports($request): bool
    {
        return $request instanceof GetHttpRequest;
    }

    protected function updateRequest(GetHttpRequest $request, Request $httpRequest): void
    {
        $request->query = $httpRequest->query->all();
        $request->request = $httpRequest->request->all();
        $request->headers = $httpRequest->headers->all();
        $request->method = $httpRequest->getMethod();
        $request->uri = $httpRequest->getUri();
        $request->clientIp = $httpRequest->getClientIp();
        $request->userAgent = $httpRequest->headers->get('User-Agent');
        $request->content = $httpRequest->getContent();
    }
}
