<?php 

class User {
    private $id;
    private $firstname;
    private $lastname;
    private $username;
    private $password;
    private $email;

    public function __construct($id = null, $firstname = null, $lastname = null, $username = null, $password = null, $email = null) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }
}

class Posts {
    private $id;
    private $userID;
    private $combinedID;
    private $header;
    private $textInput;
    private $image;
    private $imagePath;
    private $timeCreated;
    private $timeUpdated;
    private $timeUpdatedComment;

    public function __construct($id = null, $userID = null, $combinedID = null, $header = null, $textInput = null, $image = null, $imagePath = null, $timeCreated = null, $timeUpdated = null, $timeUpdatedComment = null) {
        $this->id = $id;
        $this->userID = $userID;
        $this->combinedID = $combinedID;
        $this->header = $header;
        $this->textInput = $textInput;
        $this->image = $image;
        $this->imagePath = $imagePath;
        $this->timeCreated = $timeCreated;
        $this->timeUpdated = $timeUpdated;
        $this->timeUpdatedComment = $timeUpdatedComment;
    }
}

abstract class AbstractDAO {
    protected $pdo;

    public function __construct($pdo) 
    {
        $this->pdo = $pdo;
    }

    abstract protected function getTableName();
    abstract protected function getEntityClass();

    public function findByPK() {
        $statement = $this->pdo->prepare("SELECT * FROM {$this->getTableName()} WHERE id = :id ");
    }
}

?>