<?php

namespace Eckinox\BoltNavigationUI\Utils;

use Bolt\Twig\ContentExtension;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;

/**
 * This class is heavily based on `Bolt\Menu\FrontendMenuBuilder`.
 * However, its methods are recreated here as the relevant methods
 * in `FrontendMenuBuilder` are all private. If any update is made
 * to the FrontendMenUBuilder, this class will likely need to be
 * updated as well to match those changes.
 */
class LinkHydrator
{
    /** @var Config */
    private $config;

    /** @var ContentRepository */
    private $contentRepository;

    public function __construct(
        Config $config,
        ContentRepository $contentRepository,
        ContentExtension $contentExtension
    ) {
        $this->config = $config;
        $this->contentRepository = $contentRepository;
        $this->contentExtension = $contentExtension;
    }

    public function getContent(string $link): ?Content
    {
        [$contentTypeSlug, $slug] = explode('/', $link);

        // First, try to get it if the id is numeric.
        if (is_numeric($slug)) {
            return $this->contentRepository->findOneById((int) $slug);
        }

        /** @var ContentType $contentType */
        $contentType = $this->config->getContentType($contentTypeSlug);

        return $this->contentRepository->findOneBySlug($slug, $contentType);
    }

    public function getContentTitle(string $link): string
    {
        $trimmedLink = trim($link, '/');

        // Special case for "Homepage"
        if ($trimmedLink === 'homepage' || $trimmedLink === $this->config->get('general/homepage')) {
            return 'Home';
        }

        // If it looks like `contenttype/slug`, get the Record.
        if (preg_match('/^[a-zA-Z\-\_]+\/[0-9a-zA-Z\-\_]+$/', $trimmedLink)) {
            $content = $this->getContent($trimmedLink);
            if ($content) {
                return $this->contentExtension->getTitle($content);
            }
        }

        // Otherwise trust the user. ¯\_(ツ)_/¯
        return '';
    }
}
