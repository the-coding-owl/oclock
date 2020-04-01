<?php
namespace TheCodingOwl\Oclock\Widgets;

use TYPO3\CMS\Dashboard\Widgets\AbstractWidget;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * The widget for a clock
 */
class ClockWidget extends AbstractWidget {
    /**
     * The title of the widget
     *
     * @var string
     */
    protected $title = 'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:widgets.clock.title';

    /**
     * The description of the widget
     *
     * @var string
     */
    protected $description = 'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:widgets.clock.description';

    /**
     * The name of the widget template
     *
     * @var string
     */
    protected $templateName = 'Clock';

    /**
     * The icon identifier of the widget
     *
     * @var string
     */
    protected $iconIdentifier = 'the-coding-owl-clock';

    /**
     * The width of the widget
     *
     * @var int
     */
    protected $width = 1;

    /**
     * The height of the widget
     *
     * @var int
     */
    protected $height = 1;

    /**
     * Initialize the widget view
     */
    protected function initializeView(): void {
        parent::initializeView();
        $this->view->setTemplateRootPaths(['EXT:oclock/Resources/Private/Templates/']);
        $this->view->setPartialRootPaths(['EXT:oclock/Resources/Private/Partials/']);
        $this->view->setLayoutRootPaths(['EXT:oclock/Resources/Private/Layout/']);
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Luxon');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Clock');
        $pageRenderer->addCssFile('EXT:oclock/Resources/Public/Stylesheets/dashboard.css');
        $pageRenderer->addCssLibrary('https://fontlibrary.org/face/segment14', 'stylesheet', 'screen', '', false);
    }

    /**
     * Render the widget
     */
    public function renderWidgetContent(): string {
        $this->view->assign('date', new \DateTime());
        return $this->view->render();
    }
}
