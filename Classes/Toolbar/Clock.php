<?php
namespace TheCodingOwl\Oclock\Toolbar;

use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;

class Clock implements ToolbarItemInterface {
    public function __construct() {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Clock');
    }

    public function checkAccess() {
        return TRUE;
    }

    public function getItem() {
        return '<span class="tx_oclock_time" data-time="' . (new \DateTime())->format('r') . '"></span>';
    }

    public function hasDropDown() {
        return TRUE;
    }

    public function getDropDown() {
        return '';
    }

    public function getAdditionalAttributes() {
        return '';
    }

    public function getIndex() {
        return 0;
    }
}
