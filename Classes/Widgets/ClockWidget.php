<?php
namespace TheCodingOwl\Oclock\Widgets;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Dashboard\Widgets\RequireJsModuleInterface;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * The widget for a clock
 */
class ClockWidget implements WidgetInterface, RequireJsModuleInterface, AdditionalCssInterface {
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
     * The view object
     *
     * @var StandaloneView
     */
    protected $view;

    /**
     * The extension configuration array
     *
     * @var array{dashboard:string[],additionalTemplateRootPath:string,additionalPartialRootPath:string,additionalLayoutRootPath:string}
     */
    protected $extConf = [
      'dashboard' => [
        'css' => 'EXT:oclock/Resources/Public/Stylesheets/dashboard.css'
      ],
      'additionalTemplateRootPath' => '',
      'additionalPartialRootPath' => '',
      'additionalLayoutRootPath' => ''
    ];
    
    /**
     * Constructor of the ClockWidget
     * 
     * @param StandaloneView $view
     */
    public function __construct(StandaloneView $view)
    {
        $this->view = $view;
        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        /** @var array{dashboard:string[],additionalTemplateRootPath:string,additionalPartialRootPath:string,additionalLayoutRootPath:string} $extConf */
        $extConf = $extensionConfiguration->get('oclock');
        if (is_array($extConf)) {
            $this->extConf = $extConf;
        }
    }
    
    /**
     * Initialize the widget view
     */
    protected function initializeView(): void
    {
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
        if (!empty($this->extConf['additionalTemplateRootPath'])) {
            $templateRootPaths['template'][] = $this->extConf['additionalTemplateRootPath'];
        }
        if (!empty($this->extConf['additionalPartialRootPath'])) {
            $templateRootPaths['partial'][] = $this->extConf['additionalPartialRootPath'];
        }
        if (!empty($this->extConf['additionalLayoutRootPath'])) {
            $templateRootPaths['layout'][] = $this->extConf['additionalLayoutRootPath'];
        }
        $this->view->setTemplateRootPaths($rootPaths['template']);
        $this->view->setPartialRootPaths($rootPaths['partial']);
        $this->view->setLayoutRootPaths($rootPaths['layout']);
    }

    /**
     * Render the widget
     *
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $this->view->assign('date', new \DateTime());
        return $this->view->render();
    }
    
    /**
     * Get the CSS file array
     *
     * @return string[]
     */
    public function getCssFiles(): array
    {
        return [$this->extConf['dashboard']['css']];
    }

    /**
     * Get the requireJS modules
     *
     * @return string[]
     */
    public function getRequireJsModules(): array
    {
        return [
            'TYPO3/CMS/Oclock/Luxon',
            'TYPO3/CMS/Oclock/Clock',
        ];
    }

    /**
     * @return array<string,string>
     */
    public function getOptions(): array
    {
        return [];
    }
}
