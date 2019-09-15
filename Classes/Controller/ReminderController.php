<?php

namespace TheCodingOwl\Oclock\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReminderController {
    /**
     * @var LanguageService
     */
    protected $languageService;

    /**
     * @var Connection
     */
    protected $connecction;

    public function __construct() {
        $this->languageService = GeneralUtility::makeInstance(LanguageService::class);
        $this->connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_oclock_reminder');
    }

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
            $response = new JsonResponse(
                [
                    'results' => $results,
                    'success' => TRUE,
                    'message' => ''
                ]
            );
        } catch (\Exception $e) {
            $response = new JsonResponse(
                [
                    'success' => FALSE,
                    'message' => $e->getMessage()
                ]
            );
        }

        return $response;
    }

    public function deleteAction(ServerRequestInterface $request): ResponseInterface {
        try {
            $this->connection->delete(
                'tx_oclock_reminder',
                [
                    'uid' => $request->getQueryParams()['reminder']
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
                        $queryBuilder->createNamedParameter($request->getQueryParams()['reminder'], Connection::PARAM_INT)
                    )
                );
            $params = $request->getParsedBody();
            $queryBuilder->set('message', $params['message'])
                ->set('datetime', (new \DateTime($params['datetime']))->format('Y--m-d H:i:s'));
            $changed = $queryBuilder->execute();
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
                        'LLL:EXT:oclock/Resources/Private/Language/locallang.xlf:reminder.edit.error'
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
