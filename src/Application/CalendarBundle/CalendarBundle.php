<?php

namespace Application\CalendarBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CalendarBundle extends Bundle
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
