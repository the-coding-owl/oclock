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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller for the reminders
 */
class ReminderController {
    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Constructor of the ReminderController
     */
    public function __construct() {
        $this->languageService = GeneralUtility::makeInstance(LanguageService::class);
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_oclock_reminder');
    }

    /**
     * Add a new reminder
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function addAction(ServerRequestInterface $request): ResponseInterface {
        try{
            $params = $request->getParsedBody();
            $this->connection->insert('tx_oclock_reminder', [
                'user' => $GLOBALS['BE_USER']->user['uid'],
                'message' => $params['message'],
                'datetime' => (new \DateTime($params['datetime']))->format('Y-m-d H:i:s')
            ]);
            $response = new JsonResponse([
                'success' => TRUE,
                'message' => ''
            ]);
        } catch(\Exception $e) {
            $response = new JsonResponse([
                'success' => FALSE,
                'message' => $e->getMessage()
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
            $queryBuilder = $this->connection->createQueryBuilder();
            $results = $queryBuilder->select('*')
                ->from('tx_oclock_reminder')
                ->where(
                    $queryBuilder->expr()->eq(
                        'user',
                        $queryBuilder->createNamedParameter(
                            $GLOBALS['BE_USER']->user['uid'],
                            Connection::PARAM_INT
                        )
                    )
                )
                ->setMaxResults(20)
                ->setFirstResult(0)
                ->execute()
                ->fetchAll();
            $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
            $content = '<table class="table table-striped table-hover">'
                . '<thead>'
                    . '<tr>'
                        . '<th>'
                            . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.list.message')
                        . '</th>'
                        . '<th>'
                            . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.list.date')
                        . '</th>'
                        . '<th>'
                            . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.list.edit')
                        . '</th>'
                        . '<th>'
                            . $this->languageService->sL('LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.list.delete')
                        . '</th>'
                    . '</tr>'
                . '</thead>'
                . '<tbody>';
            foreach($results as $result) {
                $content .= '<tr>'
                    . '<td class="col-responsive">'
                        . $result['message']
                    . '</td>'
                    . '<td>'
                        . (new \DateTime($result['datetime']))->format('r')
                    . '</td>'
                    . '<td>'
                        . '<a href="#" class="reminder-edit" data-uid="' . (int) $result['uid'] . '">'
                            . $iconFactory->getIcon('actions-open', Icon::SIZE_SMALL)->render()
                        . '</a>'
                    . '</td>'
                    . '<td>'
                        . '<a href="#" class="reminder-delete" data-uid="' . (int) $result['uid'] . '">'
                            . $iconFactory->getIcon('actions-delete', Icon::SIZE_SMALL)->render()
                        . '</a>'
                    . '</td>'
                . '</tr>';
            }
            if (count($results) === 0) {
                $content .= '<tr>'
                    . '<td colspan="4">'
                        . $this->languageService->sL(
                            'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.list.empty'
                        )
                    . '</td>'
                .'</tr>';
            }
            $content .= '</tbody>'
                . '</table>';
            $response = new HtmlResponse($content);
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
        try {
            $this->connection->delete(
                'tx_oclock_reminder',
                [
                    'uid' => $request->getParsedBody()['reminder'],
                    'user' => $GLOBALS['BE_USER']->user['uid']
                ]
            );
            $response = new JsonResponse(
                [
                    'message' => $this->languageService->sL(
                        'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.delete.successfull'
                    ),
                    'success' => TRUE
                ]
            );
        } catch (\Exception $e) {
            $response = new JsonResponse(
                [
                    'message' => $e->getMessage(),
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
