<?php

namespace Eckinox\BoltNavigationUI\Controller;

use Bolt\Configuration\Config;
use Bolt\Extension\ExtensionController;
use Eckinox\BoltNavigationUI\Utils\NameHelper;
use Symfony\Component\HttpFoundation\Response;

class ListController extends ExtensionController
{
    public function list(Config $config, NameHelper $nameHelper): Response
    {
        $rawMenus = $config->get("menu");
        $menus = [];

        foreach ($rawMenus as $name => $items) {
            $menus[$name] = $nameHelper->makeNameHumanFriendly($name);
        }

        return $this->render('@bolt-navigation-ui/index.html.twig', [
            "menus" => $menus,
        ]);
    }
}
