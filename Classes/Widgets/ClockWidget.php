<?php
namespace TheCodingOwl\Oclock\Widgets;

use TYPO3\CMS\Dashboard\Widgets;

/**
 * The widget for a clock
 */
class ClockWidget extends AbstractWidget {
    protected $title = 'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:widgets.clock.title';
    protected $description = 'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:widgets.clock.description';
    protected $templateName = 'Clock';
    protected $iconIdentifier = 'the-coding-owl-clock';
    protected $width = 1;
    protected $height = 1;

    /**
     * Render the widget
     */
    public function renderWidgetContent(): string {
        return $this->view->render();
    }
}
