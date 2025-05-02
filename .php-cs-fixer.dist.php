<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor') // Exclude vendor directory
    ->notPath('bootstrap.php') // Exclude specific files if needed
    ->notPath('bin/calculate-fee'); // Often CLI entry points are excluded or handled differently

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true, // Use PSR-12 standard
        'strict_param' => true, // Functions should be strictly typed
        'array_syntax' => ['syntax' => 'short'], // Use short array syntax []
        'ordered_imports' => ['sort_algorithm' => 'alpha'], // Order imports alphabetically
        'no_unused_imports' => true, // Remove unused imports
        // Add more rules as desired: https://cs.symfony.com/doc/rules/index.html
        'declare_strict_types' => true, // Add strict types declaration
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true) // Needed for rules like declare_strict_types
    ->setUsingCache(true);