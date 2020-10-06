<?php


namespace Nomess\Component\Cli\Executable;


use App\Entity\Article;
use Nomess\Component\Cli\Interactive\InteractiveInterface;
use Nomess\Component\Config\ConfigStoreInterface;

/**
 * @author Romain Lavabre <webmaster@newwebsouth.fr>
 */
class RepositoryGenerator implements ExecutableInterface
{
    
    private ConfigStoreInterface $configStore;
    private InteractiveInterface $interactive;
    private ?string              $dir = NULL;
    
    
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
        $base = $this->configStore->get( ConfigStoreInterface::DEFAULT_NOMESS )['general']['path']['default_repository'];
        
        
        $autocomplete = scandir( $this->configStore->get( ConfigStoreInterface::DEFAULT_NOMESS )['general']['path']['default_entity'] );
        
        foreach( $autocomplete as $key => &$value ) {
            if( strpos( $value, '.php' ) === FALSE ) {
                unset( $autocomplete[$key] );
                continue;
            }
            
            foreach( file( $this->configStore->get( ConfigStoreInterface::DEFAULT_NOMESS )['general']['path']['default_entity'] . $value ) as $line ) {
                if( preg_match( '/^namespace/', $line ) ) {
                    $value = trim(str_replace( [ 'namespace', ';' ], '', $line )) . '\\' . str_replace( '.php', '', $value );
                    
                    if( !class_exists( $value ) && !interface_exists( $value)) {
                        $this->interactive->writeColorRed( 'Class "' . $value . '" doesn\'t exists, or cannot be revolved' );
                        
                        return;
                    }
                    
                    if( !( new \ReflectionClass( $value ) )->isInstantiable() ) {
                        unset( $autocomplete[$key] );
                    }
                    
                    break;
                }
            }
        }
        
        $shortClassname = NULL;
        $fullClassname  = NULL;
        
        do {
            $continue = FALSE;
            
            $entityName = $this->interactive->readWithCompletion( "Entity: ", array_values($autocomplete) );
            
            if( !class_exists( $entityName ) ) {
                $this->interactive->writeColorRed( 'Class "' . $entityName . '" doesn\'t exists' );
                $continue = TRUE;
            } else {
                $shortClassname = ( new \ReflectionClass( $entityName ) )->getShortName();
                $fullClassname  = $entityName;
            }
        } while( $continue );
    
        $filename = $base . 'Repository' . $shortClassname . '.php';
        
        if(file_exists( $filename)){
            $response = $this->interactive->read( 'The file ' . str_replace($base, '', $filename) . ' already exists, overwrite ? [Y/n]');
            
            if(strpos( mb_strtolower( $response), 'y') === FALSE){
                $this->interactive->writeColorRed( 'Exit');
                return;
            }
        }
        
        file_put_contents( $filename, $this->getContent( $shortClassname, $fullClassname, $base ) );
        chown( $filename, $this->configStore->get( ConfigStoreInterface::DEFAULT_NOMESS )['server']['user'] );
        
        $this->interactive->writeColorGreen( 'Reposiotry generated' );
    }
    
    
    private function getNamespace( string $base, string $shortClassname): string
    {
        
        return 'App\\' . explode( '/', rtrim( $base, '/' ) )[count( explode( '/', rtrim( $base, '/' ) ) ) - 1];
        
        return $namespace;
    }
    
    
    private function getContent( string $shortClassname, string $fullClassname, string $base): string
    {
        
        $content = "<?php

namespace " . $this->getNamespace( $base, $shortClassname ) . ";

use Nomess\Component\Orm\EntityManagerInterface;
use Nomess\Annotations\Inject;
use $fullClassname;

class Repository$shortClassname
{
  
    /**
     * @Inject()
     */
    private EntityManagerInterface \$entityManager;

    /**
     * @return " . $shortClassname . "[]|null
     */
    public function findAll(): ?array
    {
        return \$this->entityManager->find($shortClassname::class);
    }
    
    
    /**
     * @param int|null \$id
     * @return $shortClassname|null
     */
    public function findById( ?int \$id ): ?$shortClassname
    {
        return \$this->entityManager->find($shortClassname::class, \$id);
    }
    
    
    
    
    /*
    TODO you can read this sample for help
    
    public function findByPropertyName( string \$value ): ?array
    {
        // The second argument is sql, you can use any keyword that can come after the \"WHERE\" clause
        // Strongly recommended: Insert all your parameters in the third argument, they will be escaped
        
        return \$this->entityManager->find($shortClassname::class, 'column_name = :column_name', [
            'column_name' => \$value
        ]);
        
        // When nomess/orm notices that you are using sql, it will return an array if at least one entity was found, null otherwise
        // If only one entity is expected
        
        return is_array(\$result) ? \$result[0] : null;
    }
    
    */
}
        ";
        
        return $content;
    }
}
