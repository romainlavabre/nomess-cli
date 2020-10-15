<?php


namespace Nomess\Component\Cli\Executable;


use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;

class ControllerGenerator implements ExecutableInterface
{
    
    private ConfigStoreInterface $configStore;
    private InteractiveInterface $interactive;
    private ?string $dir = NULL;
    
    
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
        $base = $this->configStore->get(ConfigStoreInterface::DEFAULT_NOMESS)['general']['path']['default_controller'];
        
        $dir = $this->interactive->read( "Precise path beginning '" . str_replace(ROOT, '', $base) . "', void for racine: " );
        
        if( $dir !== NULL ) {
            $tabDir = explode( '/', $dir );
            
            $dir = NULL;
            
            foreach( $tabDir as $value ) {
                $dir = $dir . $value . '/';
            }
            
            $this->dir = $dir;
            $base .= $dir;
        }
        
        do {
            do {
                $controllerName = $this->interactive->read( "Precise name of controller: " );
                
                if(!empty($controllerName) && strpos($controllerName, 'Controller') !== FALSE){
                    $controllerName = str_replace('Controller', '', $controllerName);
                }
            } while( empty( $this->controller = $controllerName ) );
            
            file_put_contents( $base . ucfirst( $this->controller ) . 'Controller.php', $this->getContent($base) );
            chown($base . ucfirst( $this->controller ) . 'Controller.php', $this->configStore->get(ConfigStoreInterface::DEFAULT_NOMESS)['server']['user']);
            
            $this->interactive->writeColorGreen(ucfirst($this->controller) . 'Controller generated');
            $restart = $this->interactive->read( "Pursue ? y/n [yes] " );
            
            if( $restart === NULL || $restart === 'y' || $restart === 'yes') {
                $restart = TRUE;
            } else {
                $restart = FALSE;
            }
        } while( $restart === TRUE );
    }
    
    
    private function getNamespace(string $base): string
    {
        
        $namespace = 'App\\' . explode('/', rtrim($base, '/'))[count(explode('/', rtrim($base, '/'))) - 1];
        
        $tabDir = explode( '/', $this->dir);
        
        foreach( $tabDir as $value ) {
            if( !empty( $value ) ) {
                $namespace .= '\\' . ucfirst( $value );
            }
        }
        
        return $namespace;
    }
    
    
    private function getContent(string $base): string
    {
        $content = "<?php

namespace " . $this->getNamespace($base) . ";

use Nomess\Http\HttpResponse;
use Nomess\Http\HttpRequest;
use Nomess\Annotations\Route;


/**
 * @Route(\"/" . mb_strtolower( $this->controller ) . "\")
 */
class " . ucfirst( $this->controller ) . "Controller
{
  

    /**
     * @Route(\"/\", name=\"" . mb_strtolower( $this->controller ) . ".index\", methods=\"GET\")
     * @param HttpRequest \$request
     * @param HttpResponse \$response
     * @return HttpResponse
     */
    public function index(HttpResponse \$response, HttpRequest \$request): HttpResponse
    {
        return \$response->forward(\$request)->template(\$this->getTemplate('index'));
    }

    /**
     * @Route(\"/{id}\", name=\"" . mb_strtolower( $this->controller ) . ".show\", methods=\"GET\", requirements=[\"id\" => \"[0-9]+\"])
     * @param HttpRequest \$request
     * @param HttpResponse \$response
     * @return HttpResponse
     */
    public function show(HttpResponse \$response, HttpRequest \$request): HttpResponse
    {
        return \$response->forward(\$request)->template(\$this->getTemplate('show'));
    }
    
    /**
     * @Route(\"/create\", name=\"" . mb_strtolower( $this->controller ) . ".create\", methods=\"GET,POST\")
     * @param HttpRequest \$request
     * @param HttpResponse \$response
     * @return HttpResponse
     */
    public function create(HttpResponse \$response, HttpRequest \$request): HttpResponse
    {
        return \$response->forward(\$request)->template(\$this->getTemplate('create'));
    }
    
    /**
     * @Route(\"/edit/{id}\", name=\"" . mb_strtolower( $this->controller ) . ".edit\", methods=\"GET,POST\")
     * @param HttpRequest \$request
     * @param HttpResponse \$response
     * @return HttpResponse
     */
    public function edit(HttpResponse \$response, HttpRequest \$request): HttpResponse
    {
        return \$response->forward(\$request)->template(\$this->getTemplate('edit'));
    }
    
    /**
     * @Route(\"/delete/{id}\", name=\"" . mb_strtolower( $this->controller ) . ".delete\", methods=\"GET\")
     * @param HttpRequest \$request
     * @param HttpResponse \$response
     * @return void
     */
    public function delete(HttpResponse \$response, HttpRequest \$request): void
    {
        \$response->forward(\$request)->redirectToLocal('" . mb_strtolower( $this->controller ) . ".index', NULL);
    }
    
    private function getTemplate(string \$templateName): string
    {
        return \"" . mb_strtolower( $this->controller ) . "/\$templateName.html.twig\";
    }
}
        ";
        
        return $content;
    }
}
