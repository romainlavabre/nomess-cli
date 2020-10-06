<?php


namespace Nomess\Component\Cli\Executable;


use Nomess\Component\Cache\Cli\ClearCache;
use Nomess\Component\Cli\Interactive\InteractiveInterface;

class DevelopmentBridge implements ExecutableInterface
{
    
    private const FILE = ROOT . 'public/index.php';
    private InteractiveInterface $interactive;
    private ClearCache           $clearCache;
    
    
    public function __construct(
        InteractiveInterface $interactive,
        ClearCache $clearCache )
    {
        $this->interactive = $interactive;
        $this->clearCache  = $clearCache;
    }
    
    
    public function exec( array $command ): void
    {
        $this->interactive->write( 'clear-cache' );
        $this->clearCache->exec([]);
        file_put_contents( self::FILE, $this->getContent() );
        $this->interactive->write( 'OK' );
    }
    
    
    private function getContent(): string
    {
        
        return "<?php
declare( strict_types=1 );

error_reporting( E_ALL );
ini_set( 'display_errors', 'on' );
ini_set( \"log_errors\", \"1\" );
set_time_limit( 0 );
ignore_user_abort( TRUE );


define( 'ROOT', str_replace( 'public/index.php', '', \$_SERVER['SCRIPT_FILENAME'] ) );
define( 'NOMESS_CONTEXT', 'DEV' );
ini_set( 'error_log', ROOT . 'var/log/error.log' );


require( ROOT . 'vendor/autoload.php' );
require( ROOT . 'vendor/nomess/kernel/Exception/NomessException.php' );

\$response = ( new Nomess\Initiator\Initiator() )->initializer();
\$response->show();";
    }
}
