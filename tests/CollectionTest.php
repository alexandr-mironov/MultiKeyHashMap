<?php

declare(strict_types=1);

namespace Test;

use MultiKeyHashMap\Collection;
use MultiKeyHashMap\Exception\InvalidItem;
use MultiKeyHashMap\Exception\KeyNotExists;
use PHPUnit\Framework\TestCase;

/**
 * Class CollectionTest
 *
 * @package Test
 *
 * @coversDefaultClass \MultiKeyHashMap\Collection
 */
class CollectionTest extends TestCase
{
    private static int $index = 0;

    private const OFFSET = 96;

    public function setUp(): void
    {
        self::$index = 0;
    }

    /**
     * @covers ::getMap
     * @covers ::push
     *
     * @throws InvalidItem
     * @throws KeyNotExists
     */
    public function testGetMap(): void
    {
        $collection = new Collection(TestEntity::class, 'id', 'letter');
        for ($i = 0; $i < 26; $i++) {
            $collection->push($this->getItem());
        }

        /** @var TestEntity[] $idMap */
        $idMap = $collection->getMap('id');
        /** @var TestEntity[] $letterMap */
        $letterMap = $collection->getMap('letter');

        for ($i = 0; $i < 10; $i++) {
            $randomIndex = rand(1, 26);
            $randomLetter = $this->getLetter($randomIndex);

            $hash = md5($randomIndex . $randomLetter);

            $idMapItem = $idMap[$randomIndex];
            $letterMapItem = $letterMap[$randomLetter];

            $this->assertEquals($randomIndex, $idMapItem->id);
            $this->assertEquals($randomIndex, $letterMapItem->id);
            $this->assertEquals($randomLetter, $idMapItem->letter);
            $this->assertEquals($randomLetter, $letterMapItem->letter);
            $this->assertEquals($hash, $idMapItem->getHash());
            $this->assertEquals($hash, $letterMapItem->getHash());
        }
    }

    /**
     * @return TestEntity
     */
    protected function getItem(): TestEntity
    {
        self::$index++;
        return new TestEntity(self::$index, $this->getLetter(self::$index));
    }

    /**
     * @param int $index
     *
     * @return string
     */
    protected function getLetter(int $index): string
    {
        return chr($index + self::OFFSET);
    }
}
