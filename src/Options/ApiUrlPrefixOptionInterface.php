<?php

declare(strict_types=1);

namespace RedRat\ApiHelperBundle\Options;

use Symfony\Component\HttpFoundation\Request;

interface ApiUrlPrefixOptionInterface
{
    public function isApiUrl(Request $request): bool;
}
