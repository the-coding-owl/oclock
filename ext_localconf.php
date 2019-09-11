<?php

call_user_func(function($extKey) {
    $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][] = \TheCodingOwl\Oclock\Toolbar\Clock::class;
}, 'oclock');
