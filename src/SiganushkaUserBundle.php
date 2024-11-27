<?php

declare(strict_types=1);

namespace Siganushka\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaUserBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
