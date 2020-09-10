<?php


namespace Nomess\Component\Cli\Executable;


interface ExecutableInterface
{
    public function exec(array $command): void;
}
