<?php

namespace Nails\Console\Apps;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Command\Command;

class CORE_NAILS_App extends Command
{
    protected $db;
    protected $dbTransRunning = false;

    // --------------------------------------------------------------------------

    /**
     * Confirms something with the user
     * @param  string          $question The question to confirm
     * @param  boolean         $default  The default answer
     * @param  InputInterface  $input    The Input Interface proivided by Symfony
     * @param  OutputInterface $output   The Output Interface proivided by Symfony
     * @return string
     */
    protected function confirm($question, $default, $input, $output)
    {
        $question      = is_array($question) ? implode("\n", $question) : $question;
        $helper        = $this->getHelper('question');
        $defaultString = $default ? 'Y' : 'N';
        $question      = new ConfirmationQuestion($question . ' [' . $defaultString . ']: ', $default) ;

        return $helper->ask($input, $output, $question);
    }

    // --------------------------------------------------------------------------

    /**
     * Asks the user for some input
     * @param  string          $question The question to ask
     * @param  mixed           $default  The default answer
     * @param  InputInterface  $input    The Input Interface proivided by Symfony
     * @param  OutputInterface $output   The Output Interface proivided by Symfony
     * @return string
     */
    protected function ask($question, $default, $input, $output)
    {
        $question = is_array($question) ? implode("\n", $question) : $question;
        $helper   = $this->getHelper('question');
        $question = new Question($question . ' [' . $default . ']: ', $default) ;

        return $helper->ask($input, $output, $question);
    }

    // --------------------------------------------------------------------------

    /**
     * Ensures that the database has the appropriate migrations table
     * @return boolean
     */
    protected function dbMigrationTest()
    {
        //  Test for the migrations table
        $result = $this->dbQuery('SHOW Tables LIKE \'' . NAILS_DB_PREFIX . 'migration\'')->rowCount();

        if (!$result) {

            //  Create the migrations table
            $sql = "CREATE TABLE `" . NAILS_DB_PREFIX . "migration` (
              `module` varchar(100) NOT NULL DEFAULT '',
              `version` int(11) unsigned DEFAULT NULL,
              PRIMARY KEY (`module`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            if (!(bool) $this->dbQuery($sql)) {

                return false;
            }
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Connects to the database
     * @param  OutputInterface $output The Output Interface proivided by Symfony
     * @return boolean
     */
    protected function dbConnect($output)
    {
        //  Locate the database details
        $host   = defined('DEPLOY_DB_HOST') ? DEPLOY_DB_HOST : '';
        $user   = defined('DEPLOY_DB_USERNAME') ? DEPLOY_DB_USERNAME : '';
        $pass   = defined('DEPLOY_DB_PASSWORD') ? DEPLOY_DB_PASSWORD : '';
        $dbname = defined('DEPLOY_DB_DATABASE') ? DEPLOY_DB_DATABASE : '';

        if (!defined('NAILS_DB_PREFIX')) {

            define('NAILS_DB_PREFIX', 'nails_');
        }

        // --------------------------------------------------------------------------

        //  Attempt to connect
        try {

            //  Connect...
            $this->db = new \PDO('mysql:host=' . $host. ';dbname=' . $dbname, $user, $pass);

            //  Set error mode
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return true;

        } catch(\PDOException $e) {

            $output->writeln('');
            $output->writeln('<error>Database Error:</error> ' . $e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Disconnects from the database
     * @return void
     */
    protected function dbClose()
    {
        $this->db = null;
    }

    // --------------------------------------------------------------------------

    /**
     * Starts a DB transaction
     * @return boolean
     */
    protected function dbTransactionStart()
    {
        try {

            $this->db->beginTransaction();
            $this->dbTransRunning = true;
            return true;

        } catch(\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Commits a DB transaction
     * @return void
     */
    protected function dbTransactionCommit()
    {
        try {

            $this->db->commit();
            $this->dbTransRunning = false;
            return true;

        } catch(\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Rollsback a DB transaction
     * @return void
     */
    protected function dbTransactionRollback()
    {
        try {

            $this->db->rollback();
            $this->dbTransRunning = false;
            return true;

        } catch(\Exception $e) {

            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Executes a database query
     * @param  string $sql The query to execute
     * @return void
     */
    protected function dbQuery($sql)
    {
        try {

            return $this->db->query($sql);

        } catch (\Exception $e) {

            return false;
        }
    }
}
