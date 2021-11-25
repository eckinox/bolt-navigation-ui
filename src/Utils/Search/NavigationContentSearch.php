<?php

namespace Eckinox\BoltNavigationUI\Utils\Search;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Extension\ExtensionController;
use Bolt\Repository\ContentRepository;
use Bolt\Twig\ContentExtension;
use Tightenco\Collect\Support\Collection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class NavigationContentSearch extends ExtensionController
{
    /** @var Config */
    private $config;
    
    /** @var ContentRepository */
    private $contentRepository;
    
    /** @var ContentExtension */
    private $contentExtension;
    
    /** @var RouterInterface */
    private $router;
    
    /** @var SluggerInterface */
    private $slugger;
    
    /** @var string */
    private $defaultLocale;
    
    /** @var Collection */
    private $contentTypes;

    public function __construct(Config $config, ContentRepository $contentRepository, ContentExtension $contentExtension, RouterInterface $router, SluggerInterface $slugger, string $defaultLocale)
    {
        $this->config = $config;
        $this->contentRepository = $contentRepository;
        $this->contentExtension = $contentExtension;
        $this->router = $router;
        $this->slugger = $slugger;
        $this->defaultLocale = $defaultLocale;
        $this->contentTypes = $this->getContentTypes();
    }

    /**
     * Searches in the content entities for every content type provided.
     *
     * @return array<int,NavigationSearchResult>
     */
    public function search(string $query): array
    {
        $recordResults = $this->searchInRecords($query);
        $contentTypeResults = $this->searchContentTypes($query);
        $manualUrlResults = $this->generateManualUrlResults($query);
        $results = array_merge($recordResults, $contentTypeResults, $manualUrlResults);
        $sortedResults = $this->sortResults($results, $query);

        return $sortedResults;
    }

    /**
     * @return array<int,NavigationSearchResult>
     */
    private function sortResults(array $results, string $query): array
    {
        usort($results, function(NavigationSearchResult $resultA, NavigationSearchResult $resultB) use ($query) {
            // Always put manual URLs at the end
            if ($resultA->type != $resultB->type && ($resultA->typeLabel == "URL" || $resultB->typeLabel == "URL")) {
                return $resultA->typeLabel == "URL" ? 1 : -1;
            }

            // Sort by title match first
            $titleAMatches = $this->checkIfContentMatchesQuery([$resultA->title], $query);
            $titleBMatches = $this->checkIfContentMatchesQuery([$resultB->title], $query);

            if ($titleAMatches != $titleBMatches) {
                return $titleAMatches ? -1 : 1;
            }

            // Sort by URL match next
            $urlAMatches = $this->checkIfContentMatchesQuery([$resultA->absoluteUrl], $query);
            $urlBMatches = $this->checkIfContentMatchesQuery([$resultB->absoluteUrl], $query);

            if ($urlAMatches != $urlBMatches) {
                return $urlAMatches ? -1 : 1;
            }

            return strnatcasecmp($resultA->title, $resultB->title);
        });

        return $results;
    }

    private function getContentTypes(): Collection
    {
        return $this->config->get('contenttypes')->where('searchable', true);
    }

    /**
     * Searches in the content entities for every content type provided.
     *
     * @return array<int,NavigationSearchResult>
     */
    private function searchInRecords(string $query): array
    {
        $recordsPagination = $this->contentRepository->searchNaive($query, 1, 20, $this->contentTypes);
        $results = [];

        /**
         * @var Content $content
         */
        foreach ($recordsPagination->getCurrentPageResults() as $content) {
            $locales = $content->getLocales()->all() ?: [null];

            foreach ($locales as $locale) {
                $results[] = $this->buildResultFromContent($content, $locale);
            }
        }

        return $results;
    }

    /**
     * Searches for content types whose listing page matches the search query.
     *
     * @return array<int,NavigationSearchResult>
     */
    private function searchContentTypes(string $query): array
    {
        $results = [];

        foreach ($this->contentTypes as $contentType) {
            $matchesQuery = $this->checkIfContentMatchesQuery([$contentType["name"]], $query);

            if (!$matchesQuery || $contentType["viewless_listing"]) {
                continue;
            }

            $locales = $contentType["locales"]->all() ?: [null];

            foreach ($locales as $locale) {
                $url = $contentType["slug"];

                if ($locale && $locale != $this->defaultLocale) {
                    $url = $locale . "/" . $url;
                    $absoluteUrl = $this->router->generate("listing_locale", [
                        "contentTypeSlug" => $contentType["slug"],
                        "_locale" => $locale
                    ], RouterInterface::ABSOLUTE_URL);
                } else {
                    $absoluteUrl = $this->router->generate("listing", [
                        "contentTypeSlug" => $contentType["slug"],
                    ], RouterInterface::ABSOLUTE_URL);
                }

                $results[] = new NavigationSearchResult(
                    $contentType["name"],
                    $contentType["slug"],
                    $contentType["name"],
                    $url,
                    $absoluteUrl
                );
            }

        }

        return $results;
    }

    /**
     * Returns search results for a URL manually entered by the user
     *
     * @return array<int,NavigationSearchResult>
     */
    private function generateManualUrlResults(string $query): array
    {
        // Handle absolute URL (usually external)
        if (preg_match("~^https?://.*~", $query)) {
            return [
                new NavigationSearchResult(
                    preg_replace("~^https?://(?:www.?\.)?(.+?)(?:/.*)?$~", "$1", $query),
                    "",
                    "URL",
                    $query,
                    $query
                )
            ];
        }

        // Treat the query as an internal URL
        return [
            new NavigationSearchResult(
                $query,
                "",
                "URL",
                $query,
                strpos($query, "/") === 0 ? $query : "/" . $query
            )
        ];
    }

    private function buildResultFromContent(Content $content, ?string $locale = null): NavigationSearchResult
    {
        if ($locale && $locale != $content->getDefaultLocale()) {
            $linkUrl = $locale . "/" . $content->getContentTypeSingularSlug() . "/" . $content->getSlug();
            $absoluteUrl = $this->router->generate("record_locale", [
                "_locale" => $locale,
                "slugOrId" => $content->getSlug(),
                "contentTypeSlug" => $content->getContentTypeSingularSlug(),
            ], RouterInterface::ABSOLUTE_URL);
        } else {
            $linkUrl = $content->getContentType() . "/" . $content->getId();
            $absoluteUrl = $this->router->generate("record", [
                "slugOrId" => $content->getSlug(),
                "contentTypeSlug" => $content->getContentTypeSingularSlug(),
            ], RouterInterface::ABSOLUTE_URL);
        }

        return new NavigationSearchResult(
            $this->contentExtension->getTitle($content, $locale ?: ""),
            $content->getContentType(),
            $content->getContentTypeSingularName(),
            $linkUrl,
            $absoluteUrl
        );
    }

    private function checkIfContentMatchesQuery(array $contentStrings, string $query): bool
    {
        $mergedContentString = implode(" ", $contentStrings);
        $standardizedQuery = $this->slugger->slug(strtolower(trim($query)));
        $standardizedContent = $this->slugger->slug(strtolower(trim($mergedContentString)));

        return $standardizedContent->containsAny($standardizedQuery);
    }
}
