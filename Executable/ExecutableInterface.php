<?php


namespace Nomess\Component\Cli\Executable;


interface ExecutableInterface
{
    public const CLASSNAME = 'classname';
    public const COMMENT   = 'comment';
    
    public function exec(array $command): void;
}
