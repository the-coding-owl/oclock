<?php
namespace TheCodingOwl\Oclock\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
     * Constructor of the Repository
     *
     * @param ConnectionPool $connectionPool
     */
    public function __construct(ConnectionPool $connectionPool) {
        $this->connection = $connectionPool->getConnectionForTable(self::tableName);
    }

    /**
     * Find all reminders by the user
     *
     * @param array $user
     */
    public function findAllByUser(array $user): array {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            return $queryBuilder->select('*')
                ->from(self::tableName)
                ->where(
                    $queryBuilder->expr()->eq('user', (int)$user['uid'])
                )->execute()->fetchAll();
        } catch(\Exception $e) {
            return [];
        }
    }

    public function add(array $reminder): bool {
        try {
            $this->connection->insert(self::tableName, $reminder);
            return TRUE;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return FALSE;
        }
    }

    public function remove(array $reminder): bool {
        try {
            $this->connection->delete(self::tableName, $reminder['uid']);
            return TRUE;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return FALSE;
        }
    }

    public function update(array $reminder): bool {
        try {
            $this->connection->update(self::tableName, $reminder, $reminder['uid']);
            return TRUE;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return FALSE;
        }
    }

    public function getLastErrorMessage(): string {
        return $this->connection->getLastErrorMessage();
    }
}
