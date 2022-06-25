<?php
class Dbh{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "zuriphp";

    protected function connect()
    {
        return new mysqli($this->host, $this->username, $this->password,$this->database);
    }
}
