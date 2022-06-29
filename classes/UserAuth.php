<?php
include_once 'Dbh.php';
session_start();

class UserAuth extends Dbh
{
    private static $db;

    public function __construct()
    {
        $this->db = new Dbh();
    }

    public function register($fullname, $email, $password, $confirmPassword, $country, $gender)
    {
        $conn = $this->connect();
        // password confirm check
        if ($this->confirmPasswordMatch($password, $confirmPassword)) {
            // check if user email exist
            if (!$this->checkIfEmailExist($email)) {
                $sql = "INSERT INTO Students (`full_names`, `email`, `password`, `country`, `gender`) VALUES ('$fullname','$email', '$password', '$country', '$gender')";
                if ($conn->query($sql)) {
                    echo "Ok";
                } else {
                    echo "Opps" . $conn->error;
                }
            }
            else{
                header("Location: forms/register.php?msg=Email%20Already%20Taken");
            }
        } else {
            header("Location: forms/register.php?msg=Password%20do%20not%20match!!");
        }
    }

    public function login($email, $password)
    {
        $conn = $this->connect();
        $sql = "SELECT * FROM Students WHERE email='$email' AND `password`='$password'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $_SESSION['username'] = $this->getFullNameByEmail($email);
            header("Location: dashboard.php");
        } else {
            header("Location: forms/login.php?msg_=");
        }
    }

    public function getUser($username)
    {
        $conn = $this->connect();
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }

    public function getAllUsers()
    {
        $conn = $this->connect();
        $sql = "SELECT * FROM Students";
        $result = $conn->query($sql);
        echo "<html>
        <head>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
        </head>
        <body>
        <center><h1><u> ZURI PHP STUDENTS </u> </h1> 
        <table class='table table-bordered' border='0.5' style='width: 80%; background-color: smoke; border-style: none'; >
        <tr style='height: 40px'>
            <thead class='thead-dark'> <th>ID</th><th>Full Names</th> <th>Email</th> <th>Gender</th> <th>Country</th> <th>Action</th>
        </thead></tr>";
        if ($result->num_rows > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                //show data
                echo "<tr style='height: 20px'>" .
                    "<td style='width: 50px; background: gray'>" . $data['id'] . "</td>
                    <td style='width: 150px'>" . $data['full_names'] .
                    "</td> <td style='width: 150px'>" . $data['email'] .
                    "</td> <td style='width: 150px'>" . $data['gender'] .
                    "</td> <td style='width: 150px'>" . $data['country'] .
                    "</td>
                    <td style='width: 150px'> 
                    <form action='action.php' method='post'>
                    <input type='hidden' name='id'" .
                    "value=" . $data['id'] . ">" .
                    "<button class='btn btn-danger' type='submit', name='delete'> DELETE </button> </form> </td>" .
                    "</tr>";
            }
            echo "</table></table></center></body></html>";
        }
    }

    public function deleteUser($id)
    {
        $conn = $this->connect();
        $sql = "DELETE FROM Students WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            $this->getAllUsers();
        } else {
            header("refresh:0.5; url=action.php?all=&message=Error");
        }
    }

    public function updateUser($email, $password)
    {
        $conn = $this->connect();
        $sql = "UPDATE Students SET `password` = '$password' WHERE `email` = '$email'";
        if($this->checkIfEmailExist($email)){
            // query
            $update = $conn->query($sql);
        }

        var_dump($update);
        // if ($update) {
        //     header("Location: forms/login.php?msg=Password%20reset%20successfull");
        // } else {
        //     header("Location: forms/resetpassword.php?msg=unable%20to%20reset%20password");
        // }
    }

    public function getUserByUsername($username)
    {
        $conn = $this->connect();
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }

    // solarin
    public function getFullNameByEmail($email)
    {
        $conn = $this->connect();
        $sql = "SELECT full_names FROM Students WHERE email = '$email'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                return $data['full_names'];
            }
        } else {
            return false;
        }
    }

    // solarin
    public function checkIfEmailExist($email)
    {
        $conn = $this->connect();
        $sql = "SELECT * FROM students WHERE `email` = '$email'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            return  true;
        } else {
            return false;
        }
    }

    public function logout($username)
    {
        session_start();
        session_destroy();
        header('Location: index.php');
    }

    public function confirmPasswordMatch($password, $confirmPassword)
    {
        if ($password === $confirmPassword) {
            return true;
        } else {
            return false;
        }
    }
}
