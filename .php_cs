<?php
$finder = Symfony\CS\Finder\DefaultFinder::create();
$finder->in([
    __DIR__
]);
$finder->exclude('regexes');
$finder->exclude('Tests');

$config = Symfony\CS\Config\Config::create();
$config->finder($finder);
$config->level(Symfony\CS\FixerInterface::PSR2_LEVEL);
$config->fixers([
    //for php 5.3
    'long_array_syntax'
]);

return $config;
