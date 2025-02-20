<?php
namespace TheCodingOwl\Oclock\Widgets;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\JavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Fluid\View\FluidViewFactory;

/**
 * The widget for a clock
 */
class ClockWidget implements WidgetInterface, JavaScriptInterface, AdditionalCssInterface {
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
     * @var ViewInterface
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
     */
    public function __construct()
    {
        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        /** @var array{dashboard:string[],additionalTemplateRootPath:string,additionalPartialRootPath:string,additionalLayoutRootPath:string} $extConf */
        $extConf = $extensionConfiguration->get('oclock');
        if (is_array($extConf)) {
            $this->extConf = $extConf;
        }
        $this->initializeView();
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
            $rootPaths['template'][] = $this->extConf['additionalTemplateRootPath'];
        }
        if (!empty($this->extConf['additionalPartialRootPath'])) {
            $rootPaths['partial'][] = $this->extConf['additionalPartialRootPath'];
        }
        if (!empty($this->extConf['additionalLayoutRootPath'])) {
            $rootPaths['layout'][] = $this->extConf['additionalLayoutRootPath'];
        }
        $viewData = GeneralUtility::makeInstance(
            ViewFactoryData::class,
            $rootPaths['template'],
            $rootPaths['partial'],
            $rootPaths['layout']
        );
        $viewFactory = GeneralUtility::makeInstance(FluidViewFactory::class);
        $this->view = $viewFactory->create($viewData);
    }

    /**
     * Render the widget
     *
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $this->view->assign('date', new \DateTime());
        return $this->view->render('Widget/Clock');
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
     * Get the javascript modules
     *
     * @return string[]
     */
    public function getJavaScriptModuleInstructions(): array
    {
        return [
            '@the-coding-owl/oclock/Luxon',
            '@the-coding-owl/oclock/Clock',
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
