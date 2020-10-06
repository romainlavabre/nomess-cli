<?php


namespace Nomess\Component\Cli\Command;


use Nomess\Component\Cli\Executable\CatLog;
use Nomess\Component\Cli\Executable\ClearCache;
use Nomess\Component\Cli\Executable\ControllerGenerator;
use Nomess\Component\Cli\Executable\DatabaseUpdate;
use Nomess\Component\Cli\Executable\DevelopmentBridge;
use Nomess\Component\Cli\Executable\ExecutableInterface;
use Nomess\Component\Cli\Executable\FilterGenerator;
use Nomess\Component\Cli\Executable\PackageInstall;
use Nomess\Component\Cli\Executable\ProductionBridge;
use Nomess\Component\Cli\Executable\PurgeLog;
use Nomess\Component\Cli\Executable\RepositoryGenerator;
use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Installer\InstallerHandlerInterface;

class CommandHandler implements CommandInterface
{
    
    private const MAPPER = [
        'make:controller'     => [
            ExecutableInterface::CLASSNAME => ControllerGenerator::class,
            ExecutableInterface::COMMENT   => 'Generate a controller'
        ],
        'make:filter'         => [
            ExecutableInterface::CLASSNAME => FilterGenerator::class,
            ExecutableInterface::COMMENT   => 'Generate a filter'
        ],
        'make:repository' => [
            ExecutableInterface::CLASSNAME => RepositoryGenerator::class,
            ExecutableInterface::COMMENT => 'Generate a repository' . "\n"
        ],
        'update:context:prod' => [
            ExecutableInterface::CLASSNAME => ProductionBridge::class,
            ExecutableInterface::COMMENT   => 'Pass in production context'
        ],
        'update:context:dev'  => [
            ExecutableInterface::CLASSNAME => DevelopmentBridge::class,
            ExecutableInterface::COMMENT   => 'Pass in development context' . "\n"
        ],
        'log:show'            => [
            ExecutableInterface::CLASSNAME => CatLog::class,
            ExecutableInterface::COMMENT   => 'Show your error log'
        ],
        'log:purge'           => [
            ExecutableInterface::CLASSNAME => PurgeLog::class,
            ExecutableInterface::COMMENT   => 'Purge the log' . "\n"
        ],
        'package:install' => [
            ExecutableInterface::CLASSNAME => PackageInstall::class,
            ExecutableInterface::COMMENT => 'Install a specific package'
        ]
    ];
    private InteractiveInterface      $interactive;
    private InstallerHandlerInterface $installerHandler;
    
    
    public function __construct(
        InteractiveInterface $interactive,
        InstallerHandlerInterface $installerHandler )
    {
        $this->interactive      = $interactive;
        $this->installerHandler = $installerHandler;
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
            return self::MAPPER[$response][ExecutableInterface::CLASSNAME];
        }
        
        foreach( $this->installerHandler->getPackages() as $nomessInstaller ) {
            foreach( $nomessInstaller->cli() as $command => $value ) {
                if( $command === $response ) {
                    return $value[ExecutableInterface::CLASSNAME];
                }
            }
        }
        
        $this->interactive->write( 'Command not found' );
        
        return NULL;
    }
    
    
    public function show(): void
    {
        $this->interactive->writeColorGreen( 'Welcome in console, use TAB for the autompletion of commands' );
        $this->interactive->write( '' );
        $this->interactive->writeColorYellow( 'help' . $this->getTab( 'help' ) . 'Show all commands' );
        $this->interactive->writeColorYellow( 'exit' . $this->getTab( 'exit' ) . 'Exit' );
        $this->interactive->write( '' );
        $this->interactive->writeColorGreen( '----------------------------------------------------------------------------' );
        $this->interactive->writeColorGreen( 'nomess/nomess' );
        $this->interactive->writeColorGreen( '----------------------------------------------------------------------------' );
        
        
        foreach( self::MAPPER as $command => $value ) {
            $this->interactive->writeColorYellow( $command . $this->getTab( $command ) . $value[ExecutableInterface::COMMENT] );
        }
        $this->interactive->write( '' );
        
        foreach( $this->installerHandler->getPackages() as $nomessInstaller ) {
            if( empty( $nomessInstaller->cli() ) ) {
                continue;
            }
            
            foreach( $nomessInstaller->cli() as $command => $value ) {
                if( !is_array( $value ) ) {
                    $this->interactive->writeColorGreen( '----------------------------------------------------------------------------' );
                    $this->interactive->writeColorGreen( $command );
                    $this->interactive->writeColorGreen( '----------------------------------------------------------------------------' );
                    continue;
                }
                
                $this->interactive->writeColorYellow( $command . $this->getTab( $command ) . $value[ExecutableInterface::COMMENT] );
            }
            
            $this->interactive->write( '' );
        }
    }
    
    
    public function getAllCommands(): array
    {
        
        $commands = [];
        $commands[] = 'help';
        $commands[] = 'exit';
        
        foreach( self::MAPPER as $command => $array ) {
            $commands[] = $command;
        }
        
        foreach( $this->installerHandler->getPackages() as $nomessInstaller ) {
            foreach( $nomessInstaller->cli() as $command => $value ) {
                
                if( is_array( $value ) ) {
                    $commands[] = $command;
                }
            }
        }
        
        return $commands;
    }
    
    
    private function getTab( string $command ): string
    {
        if( mb_strlen( $command ) < 8 ) {
            return "\t\t\t\t\t";
        } elseif(mb_strlen( $command ) < 16){
            return "\t\t\t\t";
        }else {
            return "\t\t\t";
        }
    }
}
