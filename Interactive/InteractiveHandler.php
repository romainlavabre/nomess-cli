<?php


namespace Nomess\Component\Cli\Interactive;


class InteractiveHandler implements InteractiveInterface
{
    public function read(string $str) {
        echo "\e[0;1m";
        $response = readline($str);
        echo "\e[0m";
        
        if(empty($response)){
            return NULL;
        }
        
        return $response;
    }
    
    public function write(string $message): void
    {
        echo "\e[0;1m";
        echo $message . "\n";
        echo "\e[0m";
    }
}
