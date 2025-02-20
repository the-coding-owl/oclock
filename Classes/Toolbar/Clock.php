<?php
namespace TheCodingOwl\Oclock\Toolbar;

use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Fluid\View\FluidViewFactory;

/**
 * Clock toolbar class
 */
class Clock implements ToolbarItemInterface {
    /**
     * @var ViewInterface
     */
    protected $view;
    
    /**
     * @var PageRenderer
     */
    protected PageRenderer $pageRenderer;
    
    /**
     * Constructs the Clock toolbar item
     */
    public function __construct() 
    {
        $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $this->pageRenderer->loadJavaScriptModule('@the-coding-owl/oclock/Luxon.js');
        $this->pageRenderer->loadJavaScriptModule('@the-coding-owl/oclock/Clock.js');
        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        /** @var array{dashboard:string[],additionalTemplateRootPath:string,additionalPartialRootPath:string,additionalLayoutRootPath:string} $extConf */
        $extConf = $extensionConfiguration->get('oclock');
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
        if (!empty($extConf['additionalTemplateRootPath'])) {
            $rootPaths['template'][] = $extConf['additionalTemplateRootPath'];
        }
        if (!empty($extConf['additionalPartialRootPath'])) {
            $rootPaths['partial'][] = $extConf['additionalPartialRootPath'];
        }
        if (!empty($extConf['additionalLayoutRootPath'])) {
            $rootPaths['layout'][] = $extConf['additionalLayoutRootPath'];
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
     * Checks the access rights to the Clock ToolbarItem
     *
     * @return bool
     */
    public function checkAccess(): bool 
    {
        return true;
    }

    /**
     * Get the DOM for the Clock ToolbarItem
     *
     * @return string
     */
    public function getItem(): string 
    {
        $this->view->assign('date', new \DateTime());
        return $this->view->render('Toolbar/Item');
    }

    /**
     * Checks if the ToolbarItem has a dropdown
     *
     * @return bool
     */
    public function hasDropDown(): bool
    {
        return true;
    }

    /**
     * Get the DOM for the dropdown
     *
     * @return string
     */
    public function getDropDown(): string
    {
        $this->view->assign('date', new \DateTime());
        return $this->view->render('Toolbar/DropDown');
    }

    /**
     * Get an array with additional attributes for the ToolbarItem container
     *
     * @return string[]
     */
    public function getAdditionalAttributes(): array
    {
        return [];
    }

    /**
     * Get the index number of the ToolbarItem, basically the position of the item
     * in the toolbar. Lower means further left, higher further right.
     *
     * @return int
     */
    public function getIndex(): int
    {
        return 0;
    }
}
