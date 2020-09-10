<?php


namespace Nomess\Component\Cli;


use Nomess\Component\Cli\Command\CommandInterface;
use Nomess\Component\Cli\Executable\ExecutableInterface;
use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;
use Nomess\Container\ContainerInterface;

class CliHandler
{
    
    private ContainerInterface   $container;
    private ConfigStoreInterface $configStore;
    private CommandInterface     $command;
    private InteractiveInterface $interactive;
    
    
    public function __construct(
        ContainerInterface $container,
        ConfigStoreInterface $configStore,
        CommandInterface $command,
        InteractiveInterface $interactive
    )
    {
        $this->container   = $container;
        $this->configStore = $configStore;
        $this->command     = $command;
        $this->interactive = $interactive;
    }
    
    
    public function listen(): void
    {
        $this->command->show();
        
        while(TRUE){
            $response = $this->interactive->read( 'Webmaster: ' );
    
            if(trim(mb_strtolower($response)) !== 'exit') {
                if(!empty($response)) {
                    $commands = $this->command->getCommand( $response );
                    $class = $this->command->getClass( $commands[0] );
    
                    if( $class !== NULL ) {
                        /** @var ExecutableInterface $instance */
                        $instance = $this->container->get( $class );
                        $instance->exec( $commands );
                    }
                }
            }else{
                break;
            }
            
        }
        
    }
}