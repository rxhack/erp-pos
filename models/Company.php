<?php
    include_once('../config/Database.php');

    class Company {
        // Class Variable
        private $database;
        private $rMessage;
        
        // Product Info
        private $cname;
        private $cid;
        private $cgst;

        public function __construct() {
            $this->database = new Database();
        }

        # Add Company
        public function add($post) {
            extract($post);

            // Clean Data
            $this->cname = htmlspecialchars(strip_tags($cname));
            $this->cid = htmlspecialchars(strip_tags($cid));
            $this->cgst = htmlspecialchars(strip_tags($cgst));

            // Sql Commands
            $sql0 = 'SELECT * from companydb WHERE cid = :cid';
            $sql1 = 'INSERT INTO companydb SET cname = :cname, cgst = :cgst, cid = :cid';

            try {
                $conn = $this->database->connect();

                // Checking For Company's Existence   
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':cid', $this->cid);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() != 0) {
                    return  array('pass' => false, 'msg' => "Company already exist!");
                }

                // Add Company
                $stmt = $conn->prepare($sql1);
                $stmt->bindParam(':cname', $this->cname);
                $stmt->bindParam(':cgst', $this->cgst);
                $stmt->bindParam(':cid', $this->cid);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }

                return array('pass' => true, 'msg' => "Company Added!");
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Search By CID
        public function searchByCid($cid) {
            // Clean Data
            $cid = htmlspecialchars(strip_tags($cid));

            // Sql Commands
            $sql0 = 'SELECT * FROM companydb WHERE cid = :cid';

            try {
                $conn = $this->database->connect();
 
                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':cid', $cid);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() == 0) {
                    return  array('pass' => true, 'company' => null);
                }
                
                return array('pass' => true, 'company' => $stmt->fetch(PDO::FETCH_ASSOC));
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }
    }