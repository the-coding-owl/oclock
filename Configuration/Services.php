<?php

declare(strict_types=1);
namespace Vendor\ExtName;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

return function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder) {
    $services = $configurator->services();

    if ($containerBuilder->hasDefinition(WidgetInterface::class)) {
        $services->set('widgets.dashboard.widget.clockWidget')
            ->class(TheCodingOwl\Oclock\Widgets\ClockWidget::class)
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->tag('dashboard.widget', [
                'identifier' => 'theCodingOwlClock',
                'groupNames' => 'systemInfo',
                'title' => 'Clock Widget',
                'description' => 'Displays a clock',
                'iconIdentifier' => 'the-coding-owl-clock',
                'height' => 'medium',
                'width' => 'medium'
            ]);
    }
};