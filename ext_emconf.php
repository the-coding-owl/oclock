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

$EM_CONF['oclock'] = array(
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
    'version' => '0.1.0-dev',
    'constraints' => array(
        'depends' => array(
            'typo3' => '10.4'
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
          'dashboard' => '10.4'
        )
    )
);
