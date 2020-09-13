<?php


namespace Nomess\Component\Cli\Executable;


use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;

class PurgeLog implements ExecutableInterface
{
    
    private InteractiveInterface $interactive;
    private ConfigStoreInterface $configStore;
    
    
    public function __construct(
        InteractiveInterface $interactive,
        ConfigStoreInterface $configStore
    )
    {
        $this->interactive = $interactive;
        $this->configStore = $configStore;
    }
    
    
    public function exec( array $command ): void
    {
        file_put_contents( $this->configStore->get( ConfigStoreInterface::DEFAULT_NOMESS)['general']['path']['default_error_log'], '');
        $this->interactive->write( 'The log was purged');
    }
}
