<?php

namespace Nomess\Component\Cli\Interactive;

interface InteractiveInterface
{
    public function read(string $str);
    
    public function write(string $message): void;
    
    public function writeColorGreen(string $message): void;
    
    public function writeColorYellow(string $message): void;
    
    public function writeColorRed(string $message): void;
    
    public function writeColorBlue(string $message): void;
    
    public function readWithCompletion(string $str, array $commands = []);
}
