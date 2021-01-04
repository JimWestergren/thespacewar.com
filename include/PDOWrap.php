<?php declare(strict_types=1);
/**
 * Simple wrapper for PDO
 * @see https://phpdelusions.net/pdo/pdo_wrapper
 * @see https://phpdelusions.net/pdo/common_mistakes
 * 
 */
class PDOWrap
{
    protected static $instance;
    protected $pdo;

    protected function __construct()
    {
        $opt  = array(

            // This is the recommended error mode. Errors are logged and not displayed in production
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // 82% faster according to my own tests https://www.jimwestergren.com/pdo-versus-mysqli
            // As long as charset = utf8mb4 it should be equally safe
            PDO::ATTR_EMULATE_PREPARES   => TRUE,
        );
        $charset = 'utf8mb4'; // UTF-8 characters larger than 3 bytes, such as emoji characters
        $dsn = 'mysql:host='.SECRET_MYSQL_HOST.';dbname='.SECRET_MYSQL_DATABASE.';charset='.$charset;
        $this->pdo = new PDO($dsn, SECRET_MYSQL_USERNAME, SECRET_MYSQL_PASSWORD, $opt);
    }

    /**
     * A classical static method to make it universally available
     * It will reconnect to MySQL only if it is not yet connected
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * A proxy to native PDO methods
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->pdo, $method), $args);
    }

    /**
     * A helper function to run prepared statements smoothly
     * @return mixed         returns from the PDO
     */
    public function run(string $sql_query, array $parameters_to_bind = [])
    {
        $stmt = $this->pdo->prepare($sql_query);
        $stmt->execute($parameters_to_bind);
        return $stmt;
    }


}

