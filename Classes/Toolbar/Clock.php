<?php
namespace TheCodingOwl\Oclock\Toolbar;

use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Clock toolbar class
 */
class Clock implements ToolbarItemInterface {
    /**
     * @var StandaloneView
     */
    protected $view;
    
    /**
     * @var PageRenderer
     */
    protected $pageRenderer;
    
    /**
     * Constructs the Clock toolbar item
     */
    public function __construct() 
    {
        $this->pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Luxon');
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Clock');
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
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
            $templateRootPaths['template'][] = $extConf['additionalTemplateRootPath'];
        }
        if (!empty($extConf['additionalPartialRootPath'])) {
            $templateRootPaths['partial'][] = $extConf['additionalPartialRootPath'];
        }
        if (!empty($extConf['additionalLayoutRootPath'])) {
            $templateRootPaths['layout'][] = $extConf['additionalLayoutRootPath'];
        }
        $this->view->setTemplateRootPaths($rootPaths['template']);
        $this->view->setPartialRootPaths($rootPaths['partial']);
        $this->view->setLayoutRootPaths($rootPaths['layout']);
        
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
        $this->view->setTemplate('Toolbar/Item');
        $this->view->assign('date', new \DateTime());
        return $this->view->render();
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
        $this->view->setTemplate('Toolbar/DropDown');
        $this->view->assign('date', new \DateTime());
        return $this->view->render();
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
