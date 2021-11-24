<?php

namespace Eckinox\BoltNavigationUI\Controller;

use Bolt\Configuration\Config;
use Bolt\Extension\ExtensionController;
use Eckinox\BoltNavigationUI\Utils\LinkHydrator;
use Eckinox\BoltNavigationUI\Utils\NameHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class EditController extends ExtensionController
{
    public function edit(string $name, NameHelper $nameHelper, LinkHydrator $linkHydrator): Response
    {
        return $this->render('@bolt-navigation-ui/edit.html.twig', [
            "menuName" => $name,
            "cleanName" => $nameHelper->makeNameHumanFriendly($name),
            "linkHydrator" => $linkHydrator,
        ]);
    }

    public function save(Request $request, Config $config): Response
    {
        $name = $request->request->get("name");
        $menusConfig = $config->get("menu")->toArray();
        $menusConfigPath = $config->getPath("config/bolt/menu");
        $encodedNavigationData = $request->request->get("encodedConfig");
        $navigationData = json_decode($encodedNavigationData, true);

        // Update the navigation in the config array
        $menusConfig[$name] = $navigationData;

        // Encode the configuration array to Yaml
        $updatedYamlConfig = Yaml::dump($menusConfig, 20);

        if (!$updatedYamlConfig) {
            return new JsonResponse(["success" => false, "msg" => "Sorry, an error occured while generating your menu's new configuration."]);
        }

        // Validate file name
        if (file_exists($menusConfigPath . ".yaml")) {
            $menusConfigPath = $menusConfigPath . ".yaml";
        } else if (file_exists($menusConfigPath . ".yml")) {
            $menusConfigPath = $menusConfigPath . ".yml";
        } else {
            return new JsonResponse(["success" => false, "msg" => "Your menu configuration file could not be found. Only Yaml menu files are supported at the moment."]);
        }

        // Save the configuration to the file
        file_put_contents($menusConfigPath, $updatedYamlConfig);

        return new JsonResponse(["success" => true, "msg" => "Your navigation has been updated successfully!"]);
    }
}
