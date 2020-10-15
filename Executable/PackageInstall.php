<?php


namespace Nomess\Component\Cli\Executable;


use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;
use Nomess\Container\Container;
use Nomess\Installer\ExecuteInstallInterface;
use Nomess\Installer\InstallerHandlerInterface;

/**
 * @author Romain Lavabre <webmaster@newwebsouth.fr>
 */
class PackageInstall implements ExecutableInterface
{
    
    private ConfigStoreInterface $configStore;
    private InteractiveInterface $interactive;
    private InstallerHandlerInterface $installerHandler;
    
    
    public function __construct(
        ConfigStoreInterface $configStore,
        InteractiveInterface $interactive,
InstallerHandlerInterface $installerHandler)
    {
        $this->configStore = $configStore;
        $this->interactive = $interactive;
        $this->installerHandler =$installerHandler;
    }
    
    
    public function exec( array $command ): void
    {
        $packages = $this->configStore->get( ConfigStoreInterface::DEFAULT_NOMESS)['packages'];
        
        if(!is_array( $packages)){
            $this->interactive->writeColorRed( 'No package to install');
            return;
        }
        
        $toInstall = $this->interactive->readWithCompletion( 'Which package to install? ', array_keys( $packages));
        
        if(!array_key_exists( $toInstall, $packages)){
            $this->interactive->writeColorRed( 'Package "' . $toInstall . '" not found');
            
            return;
        }
        
        $this->interactive->writeColorGreen( 'Installing package "' . $toInstall . '"... ');
        
        $classnameInstaller = $packages[$toInstall];
        
        foreach($this->installerHandler->getPackages() as $package){
            if(get_class($package) === $classnameInstaller){
                
                if($package->exec() === NULL){
                    $this->interactive->writeColorRed( 'No install script found');
                    return;
                }
                
                if(!(new \ReflectionClass( $package->exec()))->implementsInterface( ExecuteInstallInterface::class)){
                    $this->interactive->writeColorRed( 'An error occurred, this class does not implement "' . ExecuteInstallInterface::class . '"');
                    return;
                }
                
                Container::getInstance()->get( $package->exec())->exec();
                return;
            }
        }
    }
}
