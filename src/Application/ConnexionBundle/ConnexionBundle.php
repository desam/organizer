<?php

namespace Application\ConnexionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ConnexionBundle extends Bundle
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
