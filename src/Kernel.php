<?php
/**
 * (c) 2020
 * Author: Josh McCreight<jmccreight@shaw.ca>
 */

declare( strict_types = 1 );

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use function dirname;

/**
 * Class Kernel
 *
 * @package App
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @param ContainerConfigurator $container
     *
     * @noinspection PhpIncludeInspection
     */
    protected function configureContainer( ContainerConfigurator $container ): void
    {
        $container->import( '../config/{packages}/*.yaml' );
        $container->import( '../config/{packages}/' . $this->environment . '/*.yaml' );

        if ( is_file( dirname( __DIR__ ) . '/config/services.yaml' ) ) {
            $container->import( '../config/{services}.yaml' );
            $container->import( '../config/{services}_' . $this->environment . '.yaml' );
        } else if ( is_file( $path = dirname( __DIR__ ) . '/config/services.php' ) ) {
            ( require $path )( $container->withPath( $path ), $this );
        }
    }

    /**
     * @param RoutingConfigurator $routes
     *
     * @noinspection PhpIncludeInspection
     */
    protected function configureRoutes( RoutingConfigurator $routes ): void
    {
        $routes->import( '../config/{routes}/' . $this->environment . '/*.yaml' );
        $routes->import( '../config/{routes}/*.yaml' );

        if ( is_file( dirname( __DIR__ ) . '/config/routes.yaml' ) ) {
            $routes->import( '../config/{routes}.yaml' );
        } else if ( is_file( $path = dirname( __DIR__ ) . '/config/routes.php' ) ) {
            ( require $path )( $routes->withPath( $path ), $this );
        }
    }
}
