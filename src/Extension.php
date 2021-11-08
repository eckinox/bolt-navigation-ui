<?php

namespace Eckinox\BoltNavigationUI;

use Bolt\Extension\BaseExtension;
use Symfony\Component\Routing\Route;

class Extension extends BaseExtension
{
    public function getName(): string
    {
        return 'Bolt Navigation UI';
    }

    public function initialize(): void
    {
        $this->addTwigNamespace('bolt-navigation-ui');
    }

    public function getRoutes(): array
    {
        return [
            'bolt_eckinox_navigation_list' => new Route(
                '/bolt/navigations',
                ['_controller' => 'Eckinox\BoltNavigationUI\Controller\ListController::list']
            ),
            'bolt_eckinox_navigation_edit' => new Route(
                '/bolt/navigation/{name}',
                ['_controller' => 'Eckinox\BoltNavigationUI\Controller\EditController::edit'],
                ['name' => '[a-zA-Z0-9_\\-]+']
            ),
        ];
    }
}
