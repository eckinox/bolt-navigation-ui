<?php

namespace Eckinox\BoltNavigationUI\Utils\Search;


class NavigationSearchResult
{
    public string $title;
    public string $type;
    public string $typeLabel;
    public string $url;
    public string $absoluteUrl;

    public function __construct(string $title, string $type, string $typeLabel, string $url, string $absoluteUrl)
    {
        $this->title = $title;
        $this->type = $type;
        $this->typeLabel = $typeLabel;
        $this->url = $url;
        $this->absoluteUrl = $absoluteUrl;
    }
}
