<?php
namespace TheCodingOwl\Oclock\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Repository that handles the database stuff for reminders
 */
class ReminderRepository {
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
        }
    }

    public function remove(array $reminder): bool {

    }

    public function update(array $reminder): bool {

    }

    public function getLastErrorMessage(): string {
        return $this->connection->getLastErrorMessage();
    }
}
