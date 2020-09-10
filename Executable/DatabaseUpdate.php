<?php


namespace Nomess\Component\Cli\Executable;


use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Orm\Analyze\LauncherAnalyze;

class DatabaseUpdate implements ExecutableInterface
{
    
    private InteractiveInterface $interactive;
    private LauncherAnalyze $launcherAnalyze;
    
    public function __construct( 
        InteractiveInterface $interactive,
        LauncherAnalyze $launcherAnalyze)
    {
        $this->interactive = $interactive;
        $this->launcherAnalyze =$launcherAnalyze;
    }
    
    
    public function exec( array $command ): void
    {
        $this->launcherAnalyze->launch();
    }
}
