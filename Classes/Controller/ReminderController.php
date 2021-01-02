<?php

namespace TheCodingOwl\Oclock\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TheCodingOwl\Oclock\Domain\Repository\ReminderRepository;

/**
 * Controller for the reminders
 */
class ReminderController {
    /**
     * @var ReminderRepository
     */
    protected $reminderRepository;

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @var array
     */
    protected $user;

    /**
     * Constructor of the ReminderController
     *
     * @param ExtensionConfiguration $extensionConfiguration
     * @param StandaloneView $view
     * @param ReminderRepository $reminderRepository
     */
    public function __construct(ExtensionConfiguration $extensionConfiguration, StandaloneView $view, ReminderRepository $reminderRepository) {
        $this->view = $view;
        $this->reminderRepository = $reminderRepository;
        $this->user = $GLOBALS['BE_USER']->user;
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
     * Show the add form
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function showAddFormAction(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getQueryParams();
        $this->view->setTemplate('Reminder/AddForm');
        return new HtmlResponse($this->view->render());
    }

    /**
     * Show the edit form
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function showEditFormAction(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getQueryParams();
        $this->view->setTemplate('Reminder/EditForm');
        $this->view->assign('reminder', $this->reminderRepository->findByUidRestrictedByUser($params['reminder'], $this->user));
        return new HtmlResponse($this->view->render());
    }

    /**
     * Add a new reminder
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function addAction(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getParsedBody();
        $success = $this->reminderRepository->add($params['reminder']);
        if ($success) {
            $response = new JsonResponse([
                'success' => TRUE,
                'message' => ''
            ]);
        } else {
            $response = new JsonResponse([
                'success' => FALSE,
                'message' => $this->reminderRepository->getLastErrorMessage()
            ]);
        }

        return $response;
    }

    /**
     * Edit a reminder
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function editAction(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getParsedBody();
        $success = $this->reminderRepository->update($params['reminder']);
        if ($success) {
            $response = new JsonResponse([
                'success' => TRUE,
                'message' => ''
            ]);
        } else {
            $response = new JsonResponse([
                'success' => FALSE,
                'message' => $this->reminderRepository->getLastErrorMessage()
            ]);
        }

        return $response;
    }

    /**
     * Delete a reminder
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function deleteAction(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getParsedBody();
        $reminder = $this->reminderRepository->findByUidRestrictedByUser($params['reminder'], $this->user);
        $success = $this->reminderRepository->remove($reminder);
        if ($success) {
            $response = new JsonResponse(
                [
                    'message' => '',
                    'success' => TRUE
                ]
            );
        } else {
            $response = new JsonResponse(
                [
                    'message' => $this->reminderRepository->getLastErrorMessage(),
                    'success' => FALSE
                ]
            );
        }

        return $response;
    }
}
