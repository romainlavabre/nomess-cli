<?php


namespace Nomess\Component\Cli;


use Nomess\Component\Cli\Command\CommandHandler;
use Nomess\Component\Cli\Command\CommandInterface;
use Nomess\Component\Cli\Interactive\InteractiveHandler;
use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;

/**
 * @author Romain Lavabre <webmaster@newwebsouth.fr>
 */
class NomessInstaller implements \Nomess\Installer\NomessInstallerInterface
{
    
    public function __construct( ConfigStoreInterface $configStore )
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function container(): array
    {
        return [
            InteractiveInterface::class => InteractiveHandler::class,
            CommandInterface::class => CommandHandler::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function controller(): array
    {
        return [];
    }
    
    
    /**
     * @inheritDoc
     */
    public function cli(): array
    {
        return [];
    }
    
    
    /**
     * @inheritDoc
     */
    public function exec(): ?string
    {
        return NULL;
    }
}
