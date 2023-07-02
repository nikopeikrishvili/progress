<?php

declare(strict_types=1);

namespace Progress;

use Exception;
use Generator;

class Progress
{
    private static array $stack = [];
    private static int $progressSize = 50;

    /**
     * @param mixed $iterable
     * @param string|null $name
     * @return Generator
     * @throws Exception
     */
    public static function wrap(mixed $iterable, string $name = null): Generator
    {
        if (!is_iterable($iterable)) {
            throw new Exception('Data is not iterable');
        }
        $hash = md5(serialize($iterable));
        $nameForArray = $name ?? 'Progress ';
        self::$stack[$hash] = ['total' => count($iterable), 'current_index' => 0, 'hash' => $hash,'name'=>$nameForArray];

        foreach ($iterable as $item) {
            self::clearConsole();
            $currentHash = md5(serialize($iterable));
            foreach (self::$stack as $hash => $arrayData) {
                if ($currentHash === $hash) {
                    self::$stack[$currentHash]['current_index']++;
                }
                self::printProgress($hash);
            }

            yield $item;
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
     * @param string $hash
     * @return void
     */
    private static function printProgress(string $hash): void
    {
        $total = self::$stack[$hash]['total'];
        $currentIndex = self::$stack[$hash]['current_index'];
        $name = self::$stack[$hash]['name'];

        $progress = round(($currentIndex / $total) * self::$progressSize);
        echo $name.": [";
        echo str_repeat("=", (int)$progress);
        echo "] " . round(($currentIndex / $total) * 100) . "%\n";
    }
}