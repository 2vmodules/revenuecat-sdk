<?php

namespace Twovmodules\RevenueCat\Dto;

final class CamelToSnakeCaseTransformer
{
    public function __invoke(object $object, callable $next): mixed
    {
        $result = $next();

        if (!is_array($result)) {
            return $result;
        }

        $snakeCased = [];

        foreach ($result as $key => $value) {
            $newKey = strtolower((string) preg_replace('/[A-Z]/', '_$0', lcfirst((string) $key)));

            $snakeCased[$newKey] = $value;
        }

        return $snakeCased;
    }
}
