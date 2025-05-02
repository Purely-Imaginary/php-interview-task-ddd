<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/bin', // Include bin if desired
    ]);

    // Define sets of rules Rector should run
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
        LevelSetList::UP_TO_PHP_84, // Target your PHP version
        PHPUnitSetList::PHPUNIT_100, // Match your PHPUnit version
    ]);

    // Skip specific rules or files if needed
    // $rectorConfig->skip([
    //     \Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector::class => [
    //         __DIR__ . '/src/SomeSpecificClass.php',
    //     ],
    // ]);
};
