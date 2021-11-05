<?php

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

call_user_func(function($extKey) {
    $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][] = \TheCodingOwl\Oclock\Toolbar\Clock::class;
    if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '11.4.0', '<')) {
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'the-coding-owl-clock',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            [
                'source' => 'EXT:oclock/Resources/Public/Icons/clock.svg'
            ]
        );
    }
}, 'oclock');
