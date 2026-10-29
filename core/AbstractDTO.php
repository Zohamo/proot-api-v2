<?php

namespace Core;

abstract class AbstractDTO
{
    private static array $propCache = [];
    private static array $typeCache = [];

    /**
     * Renvoie les propriétés de l'objet.
     *
     * @return array
     */
    protected static function getProperties(): array
    {
        $class = static::class;
        if (!isset(self::$propCache[$class])) {
            $reflection = new \ReflectionClass($class);
            $properties = [];
            foreach ($reflection->getProperties() as $property) {
                $properties[$property->getName()] = $property->getType()?->getName();
            }
            self::$propCache[$class] = $properties;
            self::$typeCache[$class] = $properties;
        }
        return self::$propCache[$class];
    }

    /**
     * Renvoie le type de la propriété.
     *
     * @param  string $property
     * @return string|null
     */
    protected static function getType(string $property): ?string
    {
        return self::$typeCache[static::class][$property] ?? null;
    }

    /**
     * Alimente l'objet à partir d'un tableau.
     *
     * @param  mixed[] $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $dto = new static();
        foreach (static::getProperties() as $property => $type) {
            if (array_key_exists($property, $data)) {
                $value = $data[$property];
                $dto->{$property} = self::castValue($type, $value);
            }
        }
        return $dto;
    }

    /**
     * Convertit une valeur selon son type.
     *
     * @param  string $type
     * @param  mixed $value
     * @return mixed
     */
    protected static function castValue(?string $type, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'int'      => (int) $value,
            'float'    => (float) $value,
            'bool'     => (bool) $value,
            'string'   => (string) $value,
            'DateTime' => new \DateTime($value),
            default    => self::castCustomType($type, $value),
        };
    }

    /**
     * Convertit une valeur selon un type personnalisé.
     *
     * @param  string $type
     * @param  mixed $value
     * @return mixed
     */
    private static function castCustomType(string $type, mixed $value): mixed
    {
        if (is_subclass_of($type, AbstractDTO::class)) {
            return $type::fromArray($value);
        }

        if (str_ends_with($type, '[]')) {
            $itemType = substr($type, 0, -2);
            if (class_exists($itemType) && is_subclass_of($itemType, AbstractDTO::class)) {
                return array_map(
                    fn($item) => $itemType::fromArray($item),
                    $value
                );
            }
        }

        return enum_exists($type)
            ? $type::from($value)
            : $value;
    }
}
