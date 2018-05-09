<?php

namespace RikSomers\OMDB\Entities\Traits;

trait ParsesResults
{
    /**
     * Parses the runtime to only contain numeric characters.
     *
     * @param string $runtime
     * @return int
     */
    private function parseRuntime(string $runtime) : int
    {
        return (int) trim(str_replace('min', '', $runtime));
    }

    /**
     * Splits the given string on a comma, and trims the results
     *
     * @param string $string
     * @return array
     */
    private function splitOnComma(string $string) : array
    {
        $splitted = explode(',', $string);

        return array_map(function($value) {
            return trim($value);
        }, $splitted);
    }
}
