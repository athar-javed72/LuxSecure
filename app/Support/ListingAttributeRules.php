<?php

namespace App\Support;

use App\Models\ListingType;
use App\Models\TypeAttribute;
use Illuminate\Validation\Rule;

class ListingAttributeRules
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function forType(?ListingType $type): array
    {
        if (! $type) {
            return ['attributes' => ['nullable', 'array']];
        }

        $type->loadMissing('attributes');
        $rules = ['attributes' => ['nullable', 'array']];

        foreach ($type->attributes as $attribute) {
            $rules['attributes.'.$attribute->key] = self::rulesForAttribute($attribute);
        }

        return $rules;
    }

    /**
     * @return array<int, mixed>
     */
    public static function rulesForAttribute(TypeAttribute $attribute): array
    {
        $rules = [$attribute->is_required ? 'required' : 'nullable'];

        return match ($attribute->input_type) {
            'number' => array_merge($rules, ['numeric']),
            'boolean' => array_merge($rules, ['boolean']),
            'select' => array_merge($rules, ['string', Rule::in(self::optionValues($attribute))]),
            default => array_merge($rules, ['string', 'max:255']),
        };
    }

    /**
     * @return array<int, string>
     */
    public static function optionValues(TypeAttribute $attribute): array
    {
        $options = $attribute->options ?? [];
        if ($options === []) {
            return [];
        }

        $keys = array_keys($options);
        $isList = $keys === range(0, count($options) - 1);

        return array_map('strval', $isList ? array_values($options) : $keys);
    }
}
