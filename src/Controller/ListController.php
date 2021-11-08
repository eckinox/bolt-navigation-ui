<?php

namespace Eckinox\BoltNavigationUI\Controller;

use Bolt\Configuration\Config;
use Eckinox\BoltNavigationUI\Controller\BaseNavigationController;
use Symfony\Component\HttpFoundation\Response;

class ListController extends BaseNavigationController
{
    public function list(Config $config): Response
    {
        $rawMenus = $config->get("menu");
        $menus = [];

        foreach ($rawMenus as $name => $items) {
            $menus[$name] = $this->makeNameHumanFriendly($name);
        }

        return $this->render('@bolt-navigation-ui/index.html.twig', [
            "menus" => $menus,
        ]);
    }
}
