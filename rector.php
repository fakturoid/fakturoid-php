<?php

declare(strict_types=1);

use Rector\Set\ValueObject\LevelSetList;

return static function (\Rector\Config\RectorConfig $containerConfigurator): void {
	$containerConfigurator->paths([
        __DIR__ . '/src',
		__DIR__ . '/tests'
	]);

    $containerConfigurator->sets([
        \Rector\Set\ValueObject\DowngradeLevelSetList::DOWN_TO_PHP_74
    ]);
	$containerConfigurator->phpstanConfig( __DIR__ . '/phpstan.neon');
};
