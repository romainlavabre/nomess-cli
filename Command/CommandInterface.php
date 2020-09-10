<?php

namespace Nomess\Component\Cli\Command;

interface CommandInterface
{
    
    public function getCommand(string $response): array;
    
    
    public function getClass(string $response): ?string;
    
    public function show(): void;
}
