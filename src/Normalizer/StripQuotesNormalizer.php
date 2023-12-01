<?php

declare(strict_types=1);

namespace IntoTheVoid\Env\Normalizer;

use IntoTheVoid\Env\Helper\Str;

class StripQuotesNormalizer implements NormalizerInterface
{
    public function normalize(string $value): string
    {
        $quotes = ['"', '\''];
        foreach ($quotes as $quote) {
            $strippedValue = Str::unwrap($value, $quote, $quote);
            if ($strippedValue !== $value) {
                return $strippedValue;
            }
        }

        return $value;
    }
}
