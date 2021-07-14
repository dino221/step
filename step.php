<?php

use Behat\Behat\Context\Context;
use Behat\Behat\COntext\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;

require_once __DIR__ . '/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

class FeatureContext implements Context, SnippetAcceptingContext {
    private $db;
    private $config;
    private $output;

    public function __construct() {
        $configFileContent = file_get_contents (
            __DIR__ . '/../../config/app.json'
        );
        $this -> config = json_decode($configFileContent, true);
    }

    private function getDb(): PDO {
        if ($this->db == null) {
            $this->db = new PDO (
                "mysql:host={$this->config['host']}; "
                . "dbname =bdd_db_test",
                $this->config['user'],
                $this->config['password']
            );
        }

        return $this->db;
    }

    /**
     * @Given I do not have the "bdd_db_test" schema
     */

     public function iDoNotHaveTheSchema()
     {
         $this->executeQuery('DROP SCHEMA IF EXISTS bdd_db_test');
     }

     /**
      * @Given I not have migration files
      */

      public function iDoNotHaveMIgrationFiles() {
          exec(' rm db/migrations/* .sql > /dev/null 2>&1');
      }

      /**
       * @When I run the migrations script
       */

       public function iRunTheMigrationsScript () {

           exec('php migrate.php', $this->output);

       }

       /**
        * @Then I should have an empty migrations table
        */

        public function iShouldHaveAnEmptyMigrationsTable () {

            $migrations = $this -> getDb()
            ->query('SELECT * FROM migrations')
            ->fetch();
            assertEmpty($migrations);
        }

        private function executeQuery (string $query)
        {
            $removeSchemaCommand = sprintf (
                'mysql -u %s %s -h %s -e "%s"',
                $this->config['user'],
                empty($this->config['password'])
                ? '' : "-p{$this->config['password']}",
                $this->config['host'],
                $query
            );

            execute($removeSchemaCommand)
        }
    

}

/**
 * @Then I should get:
 */

 public function iShouldGet(PyStringNode $string) {

    assertEquals (implode("\n", $this->output), $string);
 }

 {
     "host": "127.0.0.1",
     "schema": "bdd_db_test",
     "user": "root",
     "password": ""
 }

 "autoload": {
     "psr-4": {
         "Migrations\\": "src/"
     }
 }

 <?php

 namespace Migrations;

 use Exception;
 use PDO;

 class Schema {

    const SETUP_FILE = __DIR__ .'/../db/setup.sql';
    const MIGRATIONS_DIR = __DIR__ . '/../db/migrations/';

    private $config;
    private $connection;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    private function getConnection(): PDO 
    {
        if ($this->connection === null) {
            $this->connection = new PDO (
                "mysql:host={$this->config['host']};"
                . "dbname={$this->config['schema']}",
            $this->config['user'],
            $this->config['password']
            );
        }

        return $this->connection;

    }
 }

 /**
  * @enough for today
  */