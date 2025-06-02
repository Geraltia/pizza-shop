<?php

use PHPyh\CodingStandard\PhpCsFixerCodingStandard;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/app')
    // ...
;

$config = (new PhpCsFixer\Config())
    ->setFinder($finder)
    // ...
;

(new PhpCsFixerCodingStandard())->applyTo($config, [
    // overriding rules
]);

return $config;
