<?php

namespace Twovmodules\RevenueCat\Dto;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use Exception;
use JsonSerializable;
use RuntimeException;

abstract readonly class BaseDto implements JsonSerializable
{
    public function toArray(): array
    {
        $normalizer = (new MapperBuilder())
            ->registerTransformer(new CamelToSnakeCaseTransformer())
            ->normalizer(Format::array());

        try {
            /** @var array $data */
            $data = $normalizer->normalize($this);

            return array_filter($data);
        } catch (Exception $exception) {
            throw new RuntimeException(
                'Failed to normalize object to array: ' . $exception->getMessage(),
                0,
                $exception
            );
        }
    }

    public static function fromArray(array $data): static
    {
        $mapper = (new MapperBuilder())
            ->enableFlexibleCasting()
            ->allowSuperfluousKeys()
            ->allowPermissiveTypes()
            ->mapper();

        return $mapper->map(static::class, Source::array($data)->camelCaseKeys());
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
