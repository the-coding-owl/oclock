<?php
namespace TheCodingOwl\Oclock\Toolbar;

use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TheCodingOwl\Oclock\Domain\Repository\ReminderRepository;

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
     * @var ReminderRepository
     */
    protected $reminderRepository;

    /**
     * Constructs the Clock toolbar item
     *
     * @param PageRenderer $pageRenderer
     * @param ExtensionConfiguration $extensionConfiguration
     * @param StandaloneView $view
     * @param ReminderRepository $reminderRepository
     */
    public function __construct(PageRenderer $pageRenderer, ExtensionConfiguration $extensionConfiguration, StandaloneView $view, ReminderRepository $reminderRepository)
    {
        $this->pageRenderer = $pageRenderer;
        $this->view = $view;
        $this->reminderRepository = $reminderRepository;
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Luxon');
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Clock');
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/Oclock/Reminder');
        $this->pageRenderer->addInlineLanguageLabelFile('EXT:oclock/Resources/Private/Language/backend_locallang.xlf');
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
        $this->view->assign('reminders', $this->reminderRepository->findAllByUser($this->getCurrentUser()));
        return $this->view->render();
    }

    /**
     * Get an array with additional attributes for the ToolbarItem container
     *
     * @return string[]
     */
    public function getAdditionalAttributes(): array
    {
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
    public function getIndex(): int
    {
        return 0;
    }

    /**
     * @return array
     */
    protected function getCurrentUser(): array {
        return $GLOBALS['BE_USER']->user;
    }
}
