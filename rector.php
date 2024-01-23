<?php

declare(strict_types=1);

use Rector\Set\ValueObject\LevelSetList;

return static function (\Rector\Config\RectorConfig $containerConfigurator): void {
	$containerConfigurator->paths([
		__DIR__ . '/src'
	]);

	$containerConfigurator->import(LevelSetList::UP_TO_PHP_81);

	$containerConfigurator->phpstanConfig( __DIR__ . '/phpstan.neon');
};
