<?php
    include_once('../config/Database.php');

    class Product {
        // Class Variable
        private $database;
        private $rMessage;
        
        // Product Info
        private $pname;
        private $pcode;
        private $pcolor;
        private $ptype;
        private $pfor;
        private $adderid;
        private $cid;

        public function __construct() {
            $this->database = new Database();
        }

        # Add Product
        public function add($post) {
            extract($post);

            // Clean Data
            $this->pname = htmlspecialchars(strip_tags($pname));
            $this->pcolor = htmlspecialchars(strip_tags($pcolor));
            $this->ptype = htmlspecialchars(strip_tags($ptype));
            $this->pfor = htmlspecialchars(strip_tags($pfor));
            $this->adderid = htmlspecialchars(strip_tags($adderid));
            $this->cid = htmlspecialchars(strip_tags($cid));
            $this->pcode = bin2hex(random_bytes(8));

            // Sql Commands
            $sql0 = 'SELECT * from data WHERE pcode = :pcode';
            $sql1 = 'INSERT INTO data SET pname = :pname, pcode = :pcode, pcolor = :pcolor, ptype = :ptype, pfor = :pfor, adderid = :adderid, cid = :cid';

            try {
                $conn = $this->database->connect();

                // Checking For Product Existence   
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':pcode', $this->pcode);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() != 0) {
                    return  array('pass' => false, 'msg' => "Product already exist!");
                }

                // Add Product
                $stmt = $conn->prepare($sql1);
                $stmt->bindParam(':pname', $this->pname);
                $stmt->bindParam(':pcode', $this->pcode);
                $stmt->bindParam(':pcolor', $this->pcolor);
                $stmt->bindParam(':ptype', $this->ptype);
                $stmt->bindParam(':pfor', $this->pfor);
                $stmt->bindParam(':adderid', $this->adderid);
                $stmt->bindParam(':cid', $this->cid);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }

                return array('pass' => true, 'msg' => "Product Added!");
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Update Product
        public function update($post) {
            extract($post);

            // Clean Data
            $id = htmlspecialchars(strip_tags($id));
            $this->pname = htmlspecialchars(strip_tags($pname));
            $this->pcolor = htmlspecialchars(strip_tags($pcolor));
            $this->ptype = htmlspecialchars(strip_tags($ptype));
            $this->pfor = htmlspecialchars(strip_tags($pfor));

            // Sql Commands
            $sql0 = 'UPDATE data SET pname = :pname, pcolor = :pcolor, ptype = :ptype, pfor = :pfor WHERE id = :id';

            try {
                $conn = $this->database->connect();
 
                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':pname', $pname);
                $stmt->bindParam(':pcolor', $pcolor);
                $stmt->bindParam(':ptype', $ptype);
                $stmt->bindParam(':pfor', $pfor);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                
                return array('pass' => true, 'msg' => "Product Updated!");
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Delete Product
        public function delete($id) {
            // Clean Data
            $id = htmlspecialchars(strip_tags($id));

            // Sql Commands
            $sql0 = 'DELETE FROM data WHERE id = :id';

            try {
                $conn = $this->database->connect();
 
                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                
                return array('pass' => true, 'msg' => "Product Deleted!");
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Confirm Product
        public function confirm($cid) {
            // Clean Data
            $this->cid = htmlspecialchars(strip_tags($cid));

            // Sql Commands
            $sql0 = 'INSERT INTO finaldata (cid,pname,pcode,pcolor,ptype,pfor,adderid) SELECT cid,pname,pcode,pcolor,ptype,pfor,adderid FROM data WHERE cid = :cid';
            $sql1 = 'DELETE FROM data WHERE cid = :cid';

            try {
                $conn = $this->database->connect();

                // Confirm Product
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':cid', $this->cid);
                if ($stmt->execute()) {
                    $stmt = $conn->prepare($sql1);
                    $stmt->bindParam(':cid', $this->cid);
                    if ($stmt->execute()) {} else {
                        return  array('pass' => false, 'msg' => $stmt->error);
                    }
                } else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                return array('pass' => true, 'msg' => "Product Confirmed!");
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Fetch All Products
        public function fetchAll() {
            // Sql Commands
            $sql0 = 'SELECT * FROM data';

            try {
                $conn = $this->database->connect();

                // Fetch All Products
                $stmt = $conn->prepare($sql0);

                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }

                if ($stmt->rowCount() == 0) {
                    return array('pass' => true, 'products' => null);;
                }

                return array('pass' => true, 'products' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Search Product By ID
        public function searchByID($id)  {
            // Clean Data
            $id = htmlspecialchars(strip_tags($id));

            // Sql Commands
            $sql0 = 'SELECT * FROM data WHERE id = :id';

            try {
                $conn = $this->database->connect();
 
                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() == 0) {
                    return  array('pass' => true, 'products' => null);
                }
                
                return array('pass' => true, 'products' => $stmt->fetch(PDO::FETCH_ASSOC));
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        ## Search
        # Search Products by Company ID
        public function searchByCID($cid)  {
            // Clean Data
            $this->cid = htmlspecialchars(strip_tags($cid));

            // Sql Commands
            $sql0 = 'SELECT * FROM finaldata WHERE cid = :cid';

            try {
                $conn = $this->database->connect();
 
                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':cid', $this->cid);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() == 0) {
                    return  array('pass' => true, 'products' => null);
                }
                
                return array('pass' => true, 'products' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Search Products by Company Name
        public function searchByCname($cname)  {
            // Clean Data
            $cname = htmlspecialchars(strip_tags($cname));

            // Sql Commands
            $sql0 = 'SELECT cid FROM companydb WHERE cname = :cname';
            $sql1 = 'SELECT * FROM finaldata WHERE cid = :cid';

            try {
                $conn = $this->database->connect();

                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':cname', $cname);
                if ($stmt->execute()) {
                    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $plist = array();
                    foreach ($companies as $cmp) {
                        $cid = $cmp['cid'];
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(':cid', $cid);
                        if ($stmt->execute()) {} else {
                            return  array('pass' => false, 'msg' => $stmt->error);
                        }
                        array_merge($plist, $stmt->fetchAll(PDO::FETCH_ASSOC));
                    }
                } else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }

                if (count($plist) == 0) {
                    return array('pass' => true, 'products' => null);
                }
                
                return array('pass' => true, 'products' => $plist);
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Search Products by Product Code
        public function searchByPcode($pcode)  {
            // Clean Data
            $this->pcode = htmlspecialchars(strip_tags($pcode));

            // Sql Commands
            $sql0 = 'SELECT * FROM finaldata WHERE pcode = :pcode';

            try {
                $conn = $this->database->connect();
 
                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':pcode', $this->pcode);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() == 0) {
                    return  array('pass' => true, 'products' => null);
                }
                
                return array('pass' => true, 'products' => $stmt->fetch(PDO::FETCH_ASSOC));
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Search Products by Product Name
        public function searchByPname($pname)  {
            // Clean Data
            $this->pname = htmlspecialchars(strip_tags($pname));

            // Sql Commands
            $sql0 = 'SELECT * FROM finaldata WHERE pname LIKE %:pname%';

            try {
                $conn = $this->database->connect();
 
                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':pname', $this->pname);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() == 0) {
                    return  array('pass' => true, 'products' => null);
                }
                
                return array('pass' => true, 'products' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }

        # Search Products by Product Type
        public function searchByPtype($ptype)  {
            // Clean Data
            $this->ptype = htmlspecialchars(strip_tags($ptype));

            // Sql Commands
            $sql0 = 'SELECT * FROM finaldata WHERE ptype = :ptype';

            try {
                $conn = $this->database->connect();
 
                // Checking For Users Existence
                $stmt = $conn->prepare($sql0);
                $stmt->bindParam(':ptype', $this->ptype);
                if ($stmt->execute()) {} else {
                    return  array('pass' => false, 'msg' => $stmt->error);
                }
                if ($stmt->rowCount() == 0) {
                    return  array('pass' => true, 'products' => null);
                }
                
                return array('pass' => true, 'products' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            catch(PDOException $e) {
                $this->rMessage = array('pass' => false, 'msg' => $e->getMessage());
                return $this->rMessage;
            }
        }
    }