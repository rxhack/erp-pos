<?php
    include_once('../config/Database.php');

    class User {
        // Class Variable
        private $database;
        private $rMessage;
        
        // User Info
        private $name;
        private $number;
        private $uid;

        public function __construct() {
            $this->database = new Database();
        }

        # User Registration
        public function registration($post) {
            extract($post);

            // Clear Data
            $this->name = htmlspecialchars(strip_tags($username));
            $this->number = htmlspecialchars(strip_tags($pnumber));
            $this->uid = htmlspecialchars(strip_tags($uid));
            $cuid = htmlspecialchars(strip_tags($cuid));

            // Sql Commands
            $sql0 = 'SELECT * FROM signup WHERE number = :number';
            $sql1 = 'INSERT INTO signup SET name = :name, number = :number, uid = :uid';

            $eMsg = array();
            $pass = true;

            // Data Validation
            if (strlen((string)$this->number)!=10) {
                array_push($eMsg,"ENTER A VALID NUMBER");
                $pass = false;
            }
            if(strlen((string)$this->uid)!=4 && strlen((string)$cuid)!=4) {
                array_push($eMsg,"PLEASE ENTER A 4 DIGIT UID");
                $pass = false;
            }
            if(strcmp($this->uid,$cuid) != 0) {
                array_push($eMsg,"PLEASE CONFIRM YOUR UID");
                $pass = false;
            }
            if (!$pass) {
                $this->rMessage = array('pass' => $pass, 'msg' => $eMsg);
                return $this->rMessage;
            }

            try {
                $conn = $this->database->connect();

                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':number', $this->number);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() != 0) {
                    return array('pass' => false, 'msg' => "Number already exist!");
                }

                // Register User
                $this->uid = md5($this->uid);
                $stmt = $conn->prepare($sql1);
                $stmt->bindParam(':name', $this->name);
                $stmt->bindParam(':number', $this->number);
                $stmt->bindParam(':uid', $this->uid);
                if ($stmt->execute()) {} else {
                    return array('pass' => false, 'msg' => $stmt->error);
                }
                
                $this->rMessage = array('pass' => true, 'msg' => 'SIGN UP SUCCESSFUL');
                return $this->rMessage;
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Customer Login
        public function login($post) {
            extract($post);

            // Clean Data
            $this->number = htmlspecialchars(strip_tags($pnumber));
            $this->uid = md5(htmlspecialchars(strip_tags($uid)));

            // Sql Commands
            $sql0 = 'SELECT * FROM signup WHERE number = :number AND uid = :uid';

            try {
                $conn = $this->database->connect();

                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':number', $this->number);
                $stmt->bindParam(':uid', $this->uid);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() == 0) {
                    return array('pass' => false, 'msg' => "INVALID USERNAME OR PASSWORD");
                }
                
                $this->rMessage = array('pass' => true, 'msg' => 'SIGN IN SUCCESSFUL');
                return $this->rMessage;
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

    }