<?php

interface Driver
{
    public function query($queryString, array $parameters);
}

class MysqliDatabaseDriver implements Driver
{
    public function query($queryString, array $parameters): array
    {
        return [['id' => 1, 'username' => 'msqli-test']];
    }
}

class PdoDatabaseDriver implements Driver
{
    function query($queryString, array $parameters): array
    {
        return [['id' => 2, 'username' => 'pdo-test']];
    }
}

class UserModel
{
    public $id;
    public $username;
}

class UserManager
{
    /** @var Driver */
    public $database;

    public function __construct(Driver $driver)
    {
        $this->database = $driver;
    }

    public function all()
    {
        return array_map([$this, 'hydrateUserRecord'], $this->database->query('SELECT * FROM users', []));
    }

    private function hydrateUserRecord(array $record): UserModel
    {
        $user = new UserModel();
        $user->id = $record['id'];
        $user->username = $record['username'];

        return $user;
    }
}

class UserController
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function listUsers()
    {
        return $this->userManager->all();
    }
}

//main
$driver = new PdoDatabaseDriver();
$userManager = new UserManager($driver);
$controller = new UserController($userManager);
var_dump($controller->listUsers());

