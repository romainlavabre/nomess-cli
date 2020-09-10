<?php

namespace Nomess\Component\Cli\Interactive;

interface InteractiveInterface
{
    public function read(string $str);
    
    public function write(string $message): void;
}
