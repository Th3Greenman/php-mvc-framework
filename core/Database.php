<?php


namespace app\core;


class Database
{
    public \PDO $pdo;

    /**
     * Database constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];

        $files = scandir(Application::$ROOT_DIR  . '/migrations');

        $toApplyMigrations = array_diff($files, $appliedMigrations);

        $count = 1;
        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;

            $className = pathinfo($migration, PATHINFO_FILENAME);

            $instance = new $className();

            if ($count === 1) {
                $this->log("Here we go!");
            }

            $this->log("Applying migration $migration");

            $instance->up();

            $this->log("Total applied migrations: {$count}");

            $newMigrations[] = $migration;

            $count++;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log('Already applied all migrations');
        }

    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT  CURRENT_TIMESTAMP
        ) ENGINE=INNODB;");
    }

    private function getAppliedMigrations(): array
    {
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $str = implode(',', array_map(fn($m) => "('$m')", $migrations));

        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");

        $stmt->execute();

        $this->log('New migrations are saved to database');
    }

    protected function log($message)
    {
        echo '[' . date('Y-m-d H:i:s') . ']' . ' ' . $message . PHP_EOL;
    }
}
