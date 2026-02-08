<?php
namespace TheCodingOwl\Oclock\Toolbar;

use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Fluid\View\FluidViewFactory;

/**
 * Clock toolbar class
 */
class Clock implements ToolbarItemInterface
{
    /**
     * Constructs the Clock toolbar item
     */
    public function __construct(protected readonly ExtensionConfiguration $extensionConfiguration, protected readonly FluidViewFactory $viewFactory)
    {
    }

    protected function createView(): ViewInterface
    {
        /** @var array{dashboard:string[],additionalTemplateRootPath:string,additionalPartialRootPath:string,additionalLayoutRootPath:string} $extConf */
        $extConf = $this->extensionConfiguration->get('oclock');
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
        return $this->viewFactory->create($viewData);
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
        $view = $this->createView();
        $view->assign('date', new \DateTime());
        return $view->render('Toolbar/Item');
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
        $view = $this->createView();
        $view->assign('date', new \DateTime());
        return $view->render('Toolbar/DropDown');
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
