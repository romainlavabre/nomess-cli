<?php


namespace Nomess\Component\Cli\Executable;


use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;

class FilterGenerator implements ExecutableInterface
{
    
    private ConfigStoreInterface $configStore;
    private InteractiveInterface $interactive;
    
    
    public function __construct(
        ConfigStoreInterface $configStore,
        InteractiveInterface $interactive
    )
    {
        $this->configStore = $configStore;
        $this->interactive = $interactive;
    }
    
    
    public function exec( array $command ): void
    {
        $base = $this->configStore->get(ConfigStoreInterface::DEFAULT_NOMESS)['general']['path']['default_filter'];
        
        do {
            $filtername = $this->interactive->read( "Precise the name of filter: " );
            
            if(!empty($filtername) && strpos($filtername, 'Filter') !== FALSE){
                $filtername = str_replace('Filter', '', $filtername);
            }
        } while( $filtername === NULL );
        
        file_put_contents( $base . ucfirst( $filtername ) . 'Filter.php', $this->getContent( $filtername, $base ) );
        chown($base . ucfirst( $filtername ) . 'Filter.php', $this->configStore->get(
            ConfigStoreInterface::DEFAULT_NOMESS
        )['server']['user']);
        
        $this->interactive->write('Filter generated');
    }
    
    
    private function getContent( string $name, string $base): string
    {
        return "<?php

namespace App\\" . explode('/', rtrim($base, '/'))[count(explode('/', rtrim($base, '/'))) - 1] . ";

use Nomess\Annotations\Filter;
use Nomess\Manager\FilterInterface;

/**
 * @Filter(\"your_regex_here\")
 */
class " . ucfirst( $name ) . "Filter implements FilterInterface
{
    
    public function filtrate(): void
    {
        /*
         * TODO create your rule
         *  You can use the dependency injection
         *  Use ResponseHelper for send an response
         */
    }
}";
    }
}
