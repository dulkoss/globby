<?php

namespace Globby\Contracts;

use OpenSwoole\Core\Psr\Response;
use Psr\Http\Message\ServerRequestInterface;

interface Middleware
{
    public function handle(ServerRequestInterface $request, Stack $stack): Response;
}
