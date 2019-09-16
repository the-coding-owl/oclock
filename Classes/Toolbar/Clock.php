<?php
namespace TheCodingOwl\Oclock\Toolbar;

use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * Clock toolbar class
 */
class Clock implements ToolbarItemInterface {
    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * Constructs the Clock toolbar item
     */
    public function __construct() {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Luxon');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Clock');
        $this->languageService = GeneralUtility::makeInstance(LanguageService::class);
    }

    /**
     * Checks the access rights to the Clock ToolbarItem
     *
     * @return bool
     */
    public function checkAccess(): bool {
        return TRUE;
    }

    /**
     * Get the DOM for the Clock ToolbarItem
     *
     * @return string
     */
    public function getItem(): string {
        return '<span class="server-time"></span>';
    }

    /**
     * Checks if the ToolbarItem has a dropdown
     *
     * @return bool
     */
    public function hasDropDown(): bool {
        return TRUE;
    }

    /**
     * Get the DOM for the dropdown
     *
     * @return string
     */
    public function getDropDown(): string {
        return '<p>' . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:toolbar.timezone.server') . ': <span  class="server-timezone">' . (new \DateTime())->format('e') . '</span></p>'
            . '<p>' . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:toolbar.time.server') . ': <span class="server-time">' . '</span></p>'
            . '<hr />'
            . '<p>' . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:toolbar.timezone.browser') . ': <span class="browser-timezone"></span></p>'
            . '<p>' . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:toolbar.time.browser') . ': <span class="browser-time"></span></p>';
    }

    /**
     * Get an array with additional attributes for the ToolbarItem container
     *
     * @return array
     */
    public function getAdditionalAttributes(): array {
        $currentDateTime = new \DateTime();
        return [
            'class' => 'tx_oclock',
            'data-time' => $currentDateTime->format('r'),
            'data-timezone' => $currentDateTime->format('e')
        ];
    }

    /**
     * Get the index number of the ToolbarItem, basically the position of the item
     * in the toolbar. Lower means further left, higher further right.
     *
     * @return int
     */
    public function getIndex(): int {
        return 0;
    }
}
