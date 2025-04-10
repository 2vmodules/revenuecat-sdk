<?php

namespace Twovmodules\RevenueCat\Dto\Response\WebHook;

readonly class ProductChangeEvent extends BaseWebHookEvent
{
    use CommonFieldsTrait;

    public function __construct(
        array $commonFields,
        public ?string $newProductId
    ) {
        parent::__construct(...(array) BaseWebHookEvent::fromArray($commonFields));
    }

    public static function withCommonFields(array $data): static
    {
        // @phpstan-ignore new.static
        return new static($data, $data['new_product_id'] ?? null);
    }
}
