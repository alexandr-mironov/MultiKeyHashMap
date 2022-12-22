<?php

declare(strict_types=1);

namespace Test;

class TestEntity
{
    private string $hash;

    /**
     * TestEntity constructor.
     *
     * @param int $id
     * @param string $letter
     */
    public function __construct(
        public int $id,
        public string $letter
    ) {
        $this->hash = md5($id . $letter);
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
