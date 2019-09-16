<?php
namespace TheCodingOwl\Oclock\Toolbar;

use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Localization\LanguageService;

class Clock implements ToolbarItemInterface {
    /**
     * @var LanguageService
     */
    protected $languageService;

    public function __construct() {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Luxon');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Clock');
        $this->languageService = GeneralUtility::makeInstance(LanguageService::class);
    }

    public function checkAccess() {
        return TRUE;
    }

    public function getItem() {
        return '<span class="server-time"></span>';
    }

    public function hasDropDown() {
        return TRUE;
    }

    public function getDropDown() {
        return '<p>' . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:toolbar.timezone.server') . ': <span  class="server-timezone">' . (new \DateTime())->format('e') . '</span></p>'
            . '<p>' . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:toolbar.time.server') . ': <span class="server-time">' . '</span></p>'
            . '<hr />'
            . '<p>' . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:toolbar.timezone.browser') . ': <span class="browser-timezone"></span></p>'
            . '<p>' . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:toolbar.time.browser') . ': <span class="browser-time"></span></p>';
    }

    public function getAdditionalAttributes() {
        $currentDateTime = new \DateTime();
        return [
            'class' => 'tx_oclock',
            'data-time' => $currentDateTime->format('r'),
            'data-timezone' => $currentDateTime->format('e')
        ];
    }

    public function getIndex() {
        return 0;
    }
}
