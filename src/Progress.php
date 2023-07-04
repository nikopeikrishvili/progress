<?php

declare(strict_types=1);

namespace Progress;

use Generator;

class Progress implements \IteratorAggregate
{
    private static array $stack = [];
    private static int $progressSize = 50;
    private iterable $iterable;
    private int $total;
    private int $currentIndex = 0;
    private string $name;

    /**
     * @param mixed $iterable
     * @param string|null $name
     */
    public function __construct(iterable $iterable, string $name = null)
    {
        $this->iterable = $iterable;
        $this->total = count($iterable);
        $this->name = $name ?? 'Progress ';
        self::$stack[] = $this;
    }

    public function getIterator(): Generator
    {
        foreach ($this->iterable as $key => $item) {
            $this->currentIndex++;
            self::showProgress();
            yield $key => $item;
        }

        $key = array_search($this, self::$stack, true);
        unset(self::$stack[$key]);
    }


    private static function showProgress(): void
    {
        self::clearConsole();
        foreach (self::$stack as $item) {
            $item->printProgress();
        }
    }

    /**
     * @return void
     */
    private static function clearConsole(): void
    {
        echo "\033[2J\033[H";
    }

    /**
     * @return void
     */
    private function printProgress(): void
    {
        $progress = round(($this->currentIndex / $this->total) * self::$progressSize);
        echo $this->name . ": [";
        echo str_repeat("=", (int)$progress);
        echo "] " . round(($this->currentIndex / $this->total) * 100) . "%\n";
    }
}
