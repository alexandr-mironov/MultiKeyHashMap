<?php

declare(strict_types=1);

namespace MultiKeyHashMap;

use Generator;
use Iterator;
use MultiKeyHashMap\Exception\InvalidItem;
use MultiKeyHashMap\Exception\KeyNotExists;
use MultiKeyHashMap\Exception\PropertyNotExists;

/**
 * Class Collection
 *
 * This collection has main value, and can have many key maps,
 * that's mean you can create map (based on this collection) by choosing one of any unique value in collection
 * for example:
 *  we have some data as
 *      | id | slug  | ... other properties |
 *      |  1 | alpha | .................... |
 *      |  2 | beta  | .................... |
 *
 *  we create collection
 *      ```php
 *          $collection = new Collection(SomeCLass::class, 'id', 'slug');
 *          foreach ($iterator as $item) {
 *              $instance = new SomeClass($item); // just example of entity creating
 *              $collection->push($instance);
 *          }
 *
 *          // after that you can get map by passing name of key
 *          $idMappedCollection = $collection->getMap('id'); // array with ID as key and SomeClass entity as value
 *          $slugMappedCollection = $collection->getMap('slug'); // array with SLUG as key and SomeClass entity as value
 *      ```
 */
class Collection implements Iterator
{
    /** @var object[] $collection */
    protected array $collection = [];

    /** @var int $current current item key (index) */
    protected int $current = 0;

    /**
     * @var array<string, array<object>>
     */
    protected array $keyMaps = [];

    /**
     * Collection constructor.
     *
     * @param string $entityClass class name of entity
     * @param string ...$keys should be exists properties of entity
     *
     * @throws PropertyNotExists
     */
    public function __construct(
        protected string $entityClass,
        string ...$keys
    ) {
        foreach ($keys as $key) {
            if (!property_exists($this->entityClass, $key)) {
                throw new PropertyNotExists(
                    'Property '
                    . $key
                    . ' not exists at entity '
                    . $this->entityClass
                );
            }
            $this->keyMaps[$key] = [];
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->entityClass;
    }

    /**
     * @param object $item
     *
     * @throws InvalidItem
     */
    public function push(object $item): void
    {
        if (!$this->validateItem($item)) {
            throw new InvalidItem('Provided item is not instance of ' . $this->entityClass);
        }

        $nextIndex = count($this->collection) + 1;
        $this->collection[$nextIndex] = $item;

        foreach ($this->keyMaps as $mapKey => &$collection) {
            $collection[(string)$item->$mapKey] = &$item; // ?object was set as link anyway?
        }
    }

    /**
     * @param object $item
     *
     * @return bool
     */
    public function validateItem(object $item): bool
    {
        return $item instanceof $this->entityClass;
    }

    /**
     * @param string $key
     *
     * @return object[]
     * @throws KeyNotExists
     */
    public function getMap(string $key): array
    {
        if (!array_key_exists($key, $this->keyMaps)) {
            throw new KeyNotExists("Collection can't be mapped with key " . $key);
        }

        return $this->keyMaps[$key];
    }

    /**
     * @return object
     */
    public function current(): object
    {
        return $this->collection[$this->current];
    }

    public function next(): void
    {
        ++$this->current;
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->current;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return array_key_exists($this->current, $this->collection);
    }

    public function rewind(): void
    {
        $this->current = 0;
    }

    /**
     * @param string $key
     *
     * @return Generator
     * @throws PropertyNotExists
     */
    public function getMapIterator(string $key): Generator
    {
        foreach ($this->collection as $item) {
            if (!property_exists($item, $key)) {
                throw new PropertyNotExists("Item doesn't have property " . $key);
            }
            yield ((string)$item->{$key}) => $item;
        }
    }
}
