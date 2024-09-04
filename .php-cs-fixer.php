<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

$header = <<<EOF
Copyright (c) D3 Data Development (Inh. Thomas Dartsch)

For the full copyright and license information, please view
the LICENSE file that was distributed with this source code.

https://www.d3data.de

@copyright (C) D3 Data Development (Inh. Thomas Dartsch)
@author    D3 Data Development - Daniel Seifert <info@shopmodule.com>
@link      https://www.oxidmodule.com
EOF;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PHP80Migration' => true,
        '@PSR12' => true,
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header' => $header,
            'location' => 'after_open',
            'separate' => 'both',
        ],
        'php_unit_test_class_requires_covers' => true,
        'doctrine_annotation_indentation' => true,
    ])
    ->setFinder($finder)
;
