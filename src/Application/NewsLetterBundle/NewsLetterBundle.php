<?php

namespace Application\NewsLetterBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NewsLetterBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }
}