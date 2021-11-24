<?php

namespace Eckinox\BoltNavigationUI\Controller;

use Bolt\Extension\ExtensionController;
use Eckinox\BoltNavigationUI\Utils\Search\NavigationContentSearch;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends ExtensionController
{
    public function search(Request $request, NavigationContentSearch $navigationContentSearch): Response
    {
        $query = $request->request->get("query");
        $results = $navigationContentSearch->search($query);

        return new JsonResponse([
            "records" => $results
        ]);
    }

}
