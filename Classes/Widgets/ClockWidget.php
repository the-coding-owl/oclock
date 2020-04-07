<?php
namespace TheCodingOwl\Oclock\Widgets;

use TYPO3\CMS\Dashboard\Widgets\AbstractWidget;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Dashboard\Widgets\Interfaces\RequireJsModuleInterface;
use TYPO3\CMS\Dashboard\Widgets\Interfaces\AdditionalCssInterface;

/**
 * The widget for a clock
 */
class ClockWidget extends AbstractWidget implements RequireJsModuleInterface, AdditionalCssInterface {
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
     * The extension configuration array
     *
     * @var array
     */
    protected $extConf = [];
    
    /**
     * Constructor of the ClockWidget
     */
    public function __construct() {
      parent::__construct();
      /** @var ExtensionConfiguration $extensionConfiguration */
      $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
      $this->extConf = $extensionConfiguration->get('oclock');
    }
    
    /**
     * Initialize the widget view
     */
    protected function initializeView(): void {
        parent::initializeView();
        $rootPaths = [
            'template' => [
                'EXT:oclock/Resources/Private/Templates/'
            ],
            'partial' => [
                'EXT:oclock/Resources/Private/Partials/'
            ],
            'layout' => [
                'EXT:oclock/Resources/Private/Layout/'
            ]
        ];
        if(!empty($this->extConf['additionalTemplateRootPath'])) {
            $templateRootPaths['template'][] = $this->extConf['additionalTemplateRootPath'];
        }
        if(!empty($this->extConf['additionalPartialRootPath'])) {
            $templateRootPaths['partial'][] = $this->extConf['additionalPartialRootPath'];
        }
        if(!empty($this->extConf['additionalLayoutRootPath'])) {
            $templateRootPaths['layout'][] = $this->extConf['additionalLayoutRootPath'];
        }
        $this->view->setTemplateRootPaths($rootPaths['template']);
        $this->view->setPartialRootPaths($rootPaths['partial']);
        $this->view->setLayoutRootPaths($rootPaths['layout']);
    }

    /**
     * Render the widget
     */
    public function renderWidgetContent(): string {
        $this->view->assign('date', new \DateTime());
        return $this->view->render();
    }
    
    /**
     * @return array
     */
    public function getCssFiles(): array {
        return [$this->extConf['dashboard']['css']];
    }

    /**
     * @return array
     */
    public function getRequireJsModules(): array {
        return [
            'TYPO3/CMS/Oclock/Luxon',
            'TYPO3/CMS/Oclock/Clock',
        ];
    }
}
