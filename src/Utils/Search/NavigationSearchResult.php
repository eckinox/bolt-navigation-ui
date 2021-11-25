<?php

namespace Eckinox\BoltNavigationUI\Utils\Search;


class NavigationSearchResult
{
    /** @var string */
    public $title;

    /** @var string */
    public $type;

    /** @var string */
    public $typeLabel;

    /** @var string */
    public $url;

    /** @var string */
    public $absoluteUrl;

    public function __construct(string $title, string $type, string $typeLabel, string $url, string $absoluteUrl)
    {
        $this->title = $title;
        $this->type = $type;
        $this->typeLabel = $typeLabel;
        $this->url = $url;
        $this->absoluteUrl = $absoluteUrl;
    }
}
