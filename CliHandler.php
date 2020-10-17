<?php
declare(strict_types=1);

namespace Nomess\Component\Cli;


use Nomess\Component\Cli\Command\CommandInterface;
use Nomess\Component\Cli\Executable\ExecutableInterface;
use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;
use Nomess\Container\ContainerInterface;

class CliHandler
{
    
    private ContainerInterface   $container;
    private CommandInterface     $command;
    private InteractiveInterface $interactive;
    
    
    public function __construct(
        ContainerInterface $container,
        CommandInterface $command,
        InteractiveInterface $interactive
    )
    {
        $this->container   = $container;
        $this->command     = $command;
        $this->interactive = $interactive;
    }
    
    
    public function listen(): void
    {
        $this->command->show();
        
        while(TRUE){
            $response = $this->interactive->readWithCompletion( 'Webmaster: ', $this->command->getAllCommands() );
            
            if(empty( $response)){
                $this->interactive->writeColorRed( 'No command selected');
                continue;
            }
            
            if( ($response = mb_strtolower( trim( $response ) )) !== 'exit'
                && $response !== 'help') {
                
                if(!empty($response)) {
                    $commands = $this->command->getCommand( $response );
                    $class = $this->command->getClass( $commands[0] );
    
                    if( $class !== NULL ) {
                        /** @var ExecutableInterface $instance */
                        $instance = $this->container->get( $class );
                        $instance->exec( $commands );
                    }
                }
            }elseif( $response === 'help'){
                $this->command->show();
            }else{
                break;
            }
        }
    }
}
