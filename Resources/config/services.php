<?php
/**
 * services.php
 *
 * PHP version 8
 *
 * @author     Ranaivo Razakanirina <ranaivo.razakanirina@atety.com>
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(\Trano\UtilsBundle\Util\HttpRequest::class)
        ->args([
            service(\Trano\UtilsBundle\Util\Env::class)
        ]);

    $services->set(\Trano\UtilsBundle\Util\Env::class);

    $services->set(\Trano\UtilsBundle\Util\ApiJsonResponse::class)
        ->args([
            service(\Trano\UtilsBundle\Util\Env::class)
        ]);

    $services->set(\Trano\UtilsBundle\Util\ExtendedResponse::class)
        ->args([
            service(\Trano\UtilsBundle\Util\Env::class)
        ]);
};
