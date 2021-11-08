<?php

namespace Eckinox\BoltNavigationUI;

use Bolt\Menu\ExtensionBackendMenuInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExtensionMenu implements ExtensionBackendMenuInterface
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function addItems(MenuItem $menu): void
    {
        // This adds a new heading
        $menu->addChild('Navigations Extension', [
            'extras' => [
                'name' => 'Navigations Extension',
                'type' => 'separator',
            ]
        ]);

        // This adds the link
        $menu->addChild('Manage your navigations', [
           'uri' => $this->urlGenerator->generate('bolt_eckinox_navigation_list'),
            'extras' => [
                'icon' => 'fa-user-circle'
            ]
        ]);
    }
}
