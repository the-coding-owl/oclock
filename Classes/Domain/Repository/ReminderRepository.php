<?php
namespace TheCodingOwl\Oclock\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Repository that handles the database stuff for reminders
 */
class ReminderRepository implements LoggerAwareInterface {
    use LoggerAwareTrait;

    /**
     * The tableName
     */
    const tableName = 'tx_oclock_reminder';

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var \Exception
     */
    protected $lastException;

    /**
     * @var DataHandler
     */
    protected $dataHandler;

    /**
     * Constructor of the Repository
     *
     * @param ConnectionPool $connectionPool
     * @param DataHandler $dataHandler
     */
    public function __construct(ConnectionPool $connectionPool, DataHandler $dataHandler) {
        $this->connection = $connectionPool->getConnectionForTable(self::tableName);
        $this->dataHandler = $dataHandler;
    }

    /**
     * Find all reminders by the user
     *
     * @param array $user
     * @return array
     */
    public function findAllByUser(array $user, int $offset = 0, int $limit = 20): array {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            return $queryBuilder->select('*')
                ->from(self::tableName)
                ->where(
                    $queryBuilder->expr()->eq('user', (int)$user['uid'])
                )->setMaxResults($limit)
                ->setFirstResult($offset)
                ->execute()
                ->fetchAll();
        } catch(\Exception $e) {
            $this->lastException = $e;
            return [];
        }
    }

    /**
     * Find a reminder by its uid but restrict the query by the fiven user
     *
     * @param int $uid
     * @param array $user
     */
    public function findByUidRestrictedByUser(int $uid, array $user): array {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            return $queryBuilder->select('*')
                ->from(self::tableName)
                ->where(
                    $queryBuilder->expr()->eq('user', (int)$user['uid']),
                    $queryBuilder->expr()->eq('uid', $uid)
                )->setMaxResults(1)
                ->execute()
                ->fetchAssociative();
        } catch(\Exception $e) {
            $this->lastException = $e;
            return [];
        }
    }

    /**
     * Add a new reminder
     *
     * @param array $reminder
     * @return bool
     */
    public function add(array $reminder): bool {
        try {
            $newId = StringUtility::getUniqueId('NEW');
            $reminder['pid'] = 0;
            $this->dataHandler->start([
                self::tableName => [
                    $newId => $reminder
                ]
            ],[]);
            $this->dataHandler->process_datamap();
            if (empty($this->dataHandler->errorLog)) {
                return TRUE;
            }

            $e = new \Exception(implode(PHP_EOL, $this->dataHandler->errorLog));
            $this->logger->error($e->getMessage());
            $this->lastException = $e;

            if (isset($this->dataHandler->substNEWwithIDs[$newId])) {
                // appearently there was an error, but the reminder has been saved
                return TRUE;
            }

            return FALSE;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->lastException = $e;
            return FALSE;
        }
    }

    /**
     * Remove a reminder
     *
     * @param array $reminder
     * @return bool
     */
    public function remove(array $reminder): bool {
        try {
            $this->dataHandler->start(
                [],
                [
                    self::tableName => [
                        $reminder['uid'] => [
                            'delete' => 1
                        ]
                    ]
                ]
            );
            $this->dataHandler->process_cmdmap();
            return TRUE;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->lastException = $e;
            return FALSE;
        }
    }

    /**
     * Update a reminder
     *
     * @param array $reminder
     * @return bool
     */
    public function update(array $reminder): bool {
        try {
            $this->dataHandler->start(
                [
                    self::tableName => [
                        $reminder['uid'] => $reminder
                    ]
                ],
                []
            );
            $this->dataHandler->process_datamap();
            if (empty($this->dataHandler->errorLog)) {
                return TRUE;
            }

            $e = new \Exception(implode(PHP_EOL, $this->dataHandler->errorLog));
            $this->logger->error($e->getMessage());
            $this->lastException = $e;

            return FALSE;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->lastException = $e;
            return FALSE;
        }
    }

    /**
     * Get the last error message of the repository
     *
     * @return string
     */
    public function getLastErrorMessage(): string {
        return $this->lastException->getMessage();
    }
}
