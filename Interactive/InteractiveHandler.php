<?php


namespace Nomess\Component\Cli\Interactive;


class InteractiveHandler implements InteractiveInterface
{
    public function read(string $str)
    {
        echo "\e[0;1m";
        $response = readline($str);
        echo "\e[0m";
        
        if(empty($response)){
            return NULL;
        }
        
        return $response;
    }
    
    public function readWithCompletion(string $str, array $commands = [])
    {
        echo "\e[0;1m";
        readline_completion_function( function ($input, $index) use ($commands) {
            $array = [];
        
            if(empty( $input)){
                return [];
            }
            foreach($commands as $command){
                if(strpos( $command, $input) !== FALSE){
                    $array[] = $command;
                }
            }
            
            if(!empty( $array)){
                return $array;
            }
            
            return [];
        });
        
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
