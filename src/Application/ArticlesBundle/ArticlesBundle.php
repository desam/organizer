<?php

namespace Application\ArticlesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ArticlesBundle extends Bundle
{
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }
 	
}
