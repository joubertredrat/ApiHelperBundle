<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle;

use RedRat\ApiHelperBundle\DependencyInjection\RedRatApiHelperExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiHelperBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new RedRatApiHelperExtension();
        }
        return $this->extension;
    }
}
