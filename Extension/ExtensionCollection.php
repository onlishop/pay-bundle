<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Extension;

class ExtensionCollection implements ExtensionInterface
{
    /**
     * @var ExtensionInterface[]
     */
    protected array $extensions = [];

    public function addExtension(ExtensionInterface $extension, bool $forcePrepend = false): void
    {
        $forcePrepend ?
            array_unshift($this->extensions, $extension) :
            array_push($this->extensions, $extension);
    }

    public function onPreExecute(Context $context): void
    {
        foreach ($this->extensions as $extension) {
            $extension->onPreExecute($context);
        }
    }

    public function onExecute(Context $context): void
    {
        foreach ($this->extensions as $extension) {
            $extension->onExecute($context);
        }
    }

    public function onPostExecute(Context $context): void
    {
        foreach ($this->extensions as $extension) {
            $extension->onPostExecute($context);
        }
    }
}
