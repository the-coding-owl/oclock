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
     * Constructor of the ReminderController
     *
     * @param ExtensionConfiguration $extensionConfiguration
     * @param StandaloneView $view
     * @param ReminderRepository $reminderRepository
     */
    public function __construct(ExtensionConfiguration $extensionConfiguration, StandaloneView $view, ReminderRepository $reminderRepository) {
        $this->view = $view;
        $this->reminderRepository = $reminderRepository;
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
     * @param ServerResponseInterface $request
     * @return ResponseInterface
     */
    public function showAddFormAction(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getQueryParams();
        $this->view->setTemplate('Reminder/AddForm');
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
     * List reminders
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function listAction(ServerRequestInterface $request): ResponseInterface {
        try {
            $reminders = $this->reminderRepository->findAllByUser($GLOBALS['BE_USER']->user['uid']);
            $this->view->assign('reminders', $reminders);
            $this->view->setTemplate('Reminder/List');
            return new HtmlResponse($this->view->render());
        } catch (\Exception $e) {
            $response = new HtmlResponse('<div class="panel panel-error">' . $e->getMessage() . '</div>');
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
        $success = $this->reminderRepository->remove($request->getParsedBody()['reminder']);
        if ($success) {
            $response = new JsonResponse(
                [
                    'message' => $this->languageService->sL(
                        'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.delete.successfull'
                    ),
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

    /**
     * Get a reminder
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function getAction(ServerRequestInterface $request): ResponseInterface {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $reminder = $queryBuilder->select('*')
                ->from('tx_oclock_reminder')
                ->where(
                    $queryBuilder->expr()->eq(
                        'user',
                        $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'], Connection::PARAM_INT)
                    ),
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($request->getQueryParams()['reminder'], Connection::PARAM_INT)
                    )
                )
                ->setMaxResults(1)->execute()->fetch();
            if (!$reminder) {
                $response = new JsonResponse([
                    'success' => FALSE,
                    'message' => $this->languageService->sL(
                        'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.get.error'
                    )
                ]);
            } else {
                $response = new JsonResponse([
                    'success' => TRUE,
                    'message' => '',
                    'reminder' => $reminder
                ]);
            }
        } catch (\Exception $e) {
           $response = new JsonResponse([
               'success' => FALSE,
               'message' => $e->getMessage()
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
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder->update('tx_oclock_reminder')
                ->where(
                    $queryBuilder->expr()->eq(
                        'user',
                        $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'], Connection::PARAM_INT)
                    ),
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($request->getParsedBody()['reminder'], Connection::PARAM_INT)
                    )
                );
            $params = $request->getParsedBody();
            $queryBuilder->set('message', $params['message'])
                ->set('datetime', (new \DateTime($params['datetime']))->format('Y-m-d H:i:s'));
            $changes = $queryBuilder->execute();
            if ($changes === 0) {
                $response = new JsonResponse([
                    'success' => FALSE,
                    'message' => $this->languageService->sL(
                        'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.edit.error'
                    )
                ]);
            } else {
                $response = new JsonResponse([
                    'success' => TRUE,
                    'message' => $this->languageService->sL(
                        'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.edit.success'
                    )
                ]);
            }
        } catch (\Exception $e) {
            $response = new JsonResponse([
                'success' => FALSE,
                'message' => $e->getMessage()
            ]);
        }

        return $response;
    }
}
