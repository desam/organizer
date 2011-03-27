<?php

namespace Application\CommentaireBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CommentaireBundle extends Bundle
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
