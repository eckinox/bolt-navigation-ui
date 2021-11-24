<?php

namespace Eckinox\BoltNavigationUI\Utils;

class NameHelper
{
    public function makeNameHumanFriendly(string $name)
    {
        // Space out camelCase words
        $name = preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $name);

        // Space out snake_case and kebab-case
        $name = str_replace(['-', '_'], ' ', $name);

        // Capitalize the first word
        $name = ucfirst($name);

        return $name;
    }
}
