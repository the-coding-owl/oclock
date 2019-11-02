<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Display a clock in the TYPO3 Backend',
    'description' => 'This extension provides a clock in the TYPO3 Backend and gives a few neat functions',
    'category' => 'be',
    'author' => 'Kevin Ditscheid',
    'author_email' => 'kevin@the-coding-owl.de',
    'author_company' => '',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.0.4',
    'constraints' => array(
        'depends' => array(
            'typo3' => '9.5.9-10.1.99'
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        )
    )
);
