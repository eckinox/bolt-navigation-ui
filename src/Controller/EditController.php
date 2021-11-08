<?php

namespace Eckinox\BoltNavigationUI\Controller;

use Eckinox\BoltNavigationUI\Controller\BaseNavigationController;
use Symfony\Component\HttpFoundation\Response;

class EditController extends BaseNavigationController
{
    public function edit(string $name): Response
    {
        return $this->render('@bolt-navigation-ui/edit.html.twig', [
            "menuName" => $name,
            "cleanName" => $this->makeNameHumanFriendly($name),
        ]);
    }
}
