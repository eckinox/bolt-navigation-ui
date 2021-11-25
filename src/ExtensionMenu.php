<?php

namespace Eckinox\BoltNavigationUI;

use Bolt\Menu\ExtensionBackendMenuInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExtensionMenu implements ExtensionBackendMenuInterface
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function addItems(MenuItem $menu): void
    {
        $extensionName = $this->translator->trans("extension_name", [], "navigations_ui");

        // This adds a new heading
        $menu->addChild($extensionName, [
            'extras' => [
                'name' => $extensionName,
                'type' => 'separator',
            ]
        ]);

        // This adds the link
        $menu->addChild($this->translator->trans("manage_navigations", [], "navigations_ui"), [
           'uri' => $this->urlGenerator->generate('bolt_eckinox_navigation_list'),
            'extras' => [
                'icon' => 'fa-bars'
            ]
        ]);
    }
}
