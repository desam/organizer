<?php

namespace Application\OrganizerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrganizerBundle extends Bundle
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
