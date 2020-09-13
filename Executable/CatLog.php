<?php


namespace Nomess\Component\Cli\Executable;


use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;

class CatLog implements ExecutableInterface
{
    
    private InteractiveInterface $interactive;
    private ConfigStoreInterface $configStore;
    
    
    public function __construct(
        InteractiveInterface $interactive,
        ConfigStoreInterface $configStore )
    {
        $this->interactive = $interactive;
        $this->configStore = $configStore;
    }
    
    
    public function exec( array $command ): void
    {
        $this->interactive->write( 
            readfile(
                $this->configStore->get( ConfigStoreInterface::DEFAULT_NOMESS )['general']['path']['default_error_log']
            )
        );
    }
}
