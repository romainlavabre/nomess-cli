<?php


namespace Nomess\Component\Cli\Command;


use Nomess\Component\Cli\Executable\CatLog;
use Nomess\Component\Cli\Executable\ClearCache;
use Nomess\Component\Cli\Executable\ControllerGenerator;
use Nomess\Component\Cli\Executable\DatabaseUpdate;
use Nomess\Component\Cli\Executable\DevelopmentBridge;
use Nomess\Component\Cli\Executable\FilterGenerator;
use Nomess\Component\Cli\Executable\ProductionBridge;
use Nomess\Component\Cli\Executable\PurgeLog;
use Nomess\Component\Cli\Interactive\InteractiveInterface;

class CommandHandler implements CommandInterface
{
    
    private const CLASSNAME = 'classname';
    private const COMMENT   = 'comment';
    private const MAPPER    = [
        'clear:cache'         => [
            self::CLASSNAME => ClearCache::class,
            self::COMMENT   => 'Clear all caches' . "\n"
        ],
        'make:controller'     => [
            self::CLASSNAME => ControllerGenerator::class,
            self::COMMENT   => 'Generate a controller'
        ],
        'make:filter'         => [
            self::CLASSNAME => FilterGenerator::class,
            self::COMMENT   => 'Generate a filter' . "\n"
        ],
        'update:context:prod' => [
            self::CLASSNAME => ProductionBridge::class,
            self::COMMENT   => 'Pass in production context'
        ],
        'update:context:dev'  => [
            self::CLASSNAME => DevelopmentBridge::class,
            self::COMMENT   => 'Pass in development context' . "\n"
        ],
        'database:install' => [
            self::CLASSNAME => DatabaseUpdate::class,
            self::COMMENT => 'Install your database (drink a coffee)'
        ],
        'database:update' => [
            self::CLASSNAME => DatabaseUpdate::class,
            self::COMMENT => 'Update your database' . "\n"
        ],
        'log:show' => [
            self::CLASSNAME => CatLog::class,
            self::COMMENT => 'Show your error log'
        ],
        'log:purge' => [
            self::CLASSNAME => PurgeLog::class,
            self::COMMENT => 'Purge the log'
        ]
    ];
    private InteractiveInterface $interactive;
    
    
    public function __construct( InteractiveInterface $interactive )
    {
        $this->interactive = $interactive;
    }
    
    
    public function getCommand( string $response ): array
    {
        $commands = explode( ' ', $response );
        
        foreach( $commands as &$command ) {
            $command = trim( $command );
        }
        
        return $commands;
    }
    
    
    public function getClass( string $response ): ?string
    {
        if( array_key_exists( $response, self::MAPPER ) ) {
            return self::MAPPER[$response][self::CLASSNAME];
        }
        
        $this->interactive->write( 'Command not found' );
        
        return NULL;
    }
    
    
    public function show(): void
    {
        foreach( self::MAPPER as $command => $value ) {
            $this->interactive->write( $command . $this->getTab($command) . $value[self::COMMENT] );
        }
    }
    
    private function getTab(string $command): string
    {
        if(mb_strlen($command) < 16){
            return "\t\t\t\t";
        }else{
            return "\t\t\t";
        }
    }
}
