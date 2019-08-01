<?php
    session_start();
    if(!isset($_SESSION['user'])) {
        header('location: ./');
    }

    include_once '../models/Company.php';
    include_once '../models/Product.php';

    $company = new Company();
    $product = new Product();

    $cname = "";
    $cid = "";
    $cgst = "";

    if (isset($_SESSION['cid'])) {
        $r = $company->searchByCid($_SESSION['cid']);
        if ($r['pass']) {
            if ($r['company'] != null) {
                $cname = $r['company']['cname'];
                $cgst = $r['company']['cgst'];
                $cid = $r['company']['cid'];
            }
        } else {
            header('location: ./user.php?failed='.$r['msg']);
        }
    }

    if (isset($_POST['save'])) {
        if (isset($_SESSION['cid'])) {
            $r = $company->add($_POST);
        }
        $r = $product->add($_POST);
        if ($r['pass']) {
            $_SESSION['cid'] = $_POST['cid'];
            header('location: ./user.php?success='.$r['msg']);
        } else {
            header('location: ./user.php?failed='.$r['msg']);
        }
    }

    if(isset($_POST['update'])){
        $r = $product->update($_POST);
        if ($r['pass']) {
            header('location: ./user.php?success='.$r['msg']);
        } else {
            header('location: ./user.php?failed='.$r['msg']);
        }
    }

    if(isset($_GET['delete'])){
        $id = $_GET['delete'];
        $r = $product->delete($id);
        if ($r['pass']) {
            header('location: ./user.php?success='.$r['msg']);
        } else {
            header('location: ./user.php?failed='.$r['msg']);
        }
    }

    if(isset($_GET['confirm'])){
        $r = $product->confirm($_SESSION['cid']);
        if ($r['pass']) {
            unset($_SESSION['cid']);
            header('location: ./user.php?success='.$r['msg']);
        } else {
            header('location: ./user.php?failed='.$r['msg']);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $_SESSION["user"]; ?> PROFILE</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style0.css">
    <style>
        .bxs{
            text-align:center;
            border:1px solid #262626;
            height:37px;
            border-radius: 20px;
            outline:none;
        }
        .bxs:focus{
            transition:0.2s;
            border:2px solid #262626;
        }
    </style> 
</head>
<body>
    <header>
        <nav class="navbar navbar-light bg-light">
            <a class="navbar-brand" href="#"><?php echo $_SESSION["user"];?></a>
            <a style="float:right;"class="nav-link" href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">

        <?php
            if(isset($_GET['success'])) {
                echo '<div class="alert alert-success" role="alert">'.$_GET['success'].'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else if (isset($_GET['failed'])) {
                echo '<div class="alert alert-danger" role="alert">'.$_GET['failed'].'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        ?>

        <form method="post">
            <input type="hidden" name="adderid" value="<?php echo $_SESSION['user']; ?>" >
            <label>company name</label><br>
            <input type="text"name="cname"placeholder="enter company name" value="<?php echo $cname; ?>" class="form-control" required ><br>
            <label>company ID</label><br>
            <input type="text"name="cid"placeholder="enter company ID" value="<?php echo $cid; ?>" class="form-control" required ><br>
            <label>company GST</label><br>
            <input type="text"name="cgst"placeholder="enter company GST no" value="<?php echo $cgst; ?>" class="form-control" required ><br>
            <label>product name</label><br>
            <input type="text"name="pname"placeholder="enter product name" class="form-control" required><br>
            <label>product code</label><br>
            <input type="text"name="pcode"placeholder="auto Generated" class="form-control" required disabled><br>
            <label>product color</label><br>
            <input type="text"name="pcolor"placeholder="enter product color" class="form-control" required><br>
            <label>product type</label><br>
            <input type="text"name="ptype"placeholder="enter product type" class="form-control" required><br>
            <label>product for</label><br>
            <input type="text"name="pfor"placeholder="enter product for" class="form-control" required><br>
            <button type="submit"name="save" class="btn btn-primary">save</button>
        </form>
    </div>

    <div class="table-responsive"> 
        <div style="width:90%;margin:auto;margin-bottom:20px;">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">company ID</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Product Code</th>
                        <th scope="col">Product Color</th>
                        <th scope="col">Product type</th>
                        <th scope="col">Product for</th>
                        <th scope="col">added by</th>
                        <th scope="col"colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if (isset($_GET['edit'])) {
                        $id = $_GET['edit'];
                        $r = $product->searchByID($id);
                        if (!$r['pass']) {
                ?>
                    <tr>
                        <td scope="col"colspan="9"><?php echo $r['msg']; ?></td>
                    </tr>
                <?php
                        } else {
                            if ($r['products'] == null) {
                ?>
                    <tr>
                        <td scope="col"colspan="9">Product Dose not Exist!</td>
                    </tr>
                <?php
                            } else {
                                $cid=$r['products']['cid'];
                                $tpname=$r['products']['pname'];
                                $tpcode=$r['products']['pcode'];
                                $tpcolor=$r['products']['pcolor'];
                                $tptype=$r['products']['ptype'];
                                $tpfor=$r['products']['pfor'];
                                $tpby=$r['products']['adderid'];
                ?>
                <form method="POST">
                <tr>
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <td><input type="text"name="cid" value="<?php echo $cid; ?>"class="bxs" disabled><br></td>
                    <td><input type="text"name="pname" value="<?php echo $tpname; ?>"class="bxs" required><br></td>
                    <td><input type="text"name="pcode"value="<?php echo $tpcode; ?>"class="bxs" required disabled><br></td>
                    <td><input type="text"name="pcolor" value="<?php echo $tpcolor; ?>"class="bxs" required><br></td>
                    <td><input type="text"name="ptype" value="<?php echo $tptype; ?>"class="bxs" required><br></td>
                    <td><input type="text"name="pfor" value="<?php echo $tpfor; ?>"class="bxs" required><br></td>
                    <td><input type="text"name="pby" value="<?php echo $tpby; ?>"class="bxs" disabled><br></td>
                    <td><button type="submit"name="update" class="btn btn-success">update</button></td>
                </tr>
                </form>
                <?php
                            }
                        }
                    } else {
                        $r = $product->fetchAll();
                        if (!$r['pass']) {
                ?>
                    <tr>
                        <td scope="col"colspan="9"><?php echo $r['msg']; ?></td>
                    </tr>
                <?php
                        } else {
                            if ($r['products'] == null) {
                ?>
                    <tr>
                        <td scope="col"colspan="9">Add Product</td>
                    </tr>
                <?php
                            } else {
                                foreach ($r['products'] as $pro) {
                ?>
                    <tr>
                        <td><?php echo $pro["cid"];?></td>
                        <td><?php echo $pro["pname"];?></td>
                        <td><?php echo $pro["pcode"];?></td>
                        <td><?php echo $pro["pcolor"];?></td>
                        <td><?php echo $pro["ptype"];?></td>
                        <td><?php echo $pro["pfor"];?></td>
                        <td><?php echo $pro["adderid"];?></td>
                        <td><button class="btn btn-primary"><a style="color:White;"href="user.php?edit=<?php echo$pro["id"];?>">EDIT</a></button></td>
                        <td><button class="btn btn-danger"><a style="color:White;"href="user.php?delete=<?php echo$pro["id"];?>"onclick="return confirm('Are you sure?')">Delete</a></button></td>
                    </tr>
                <?php
                                }
                            }
                        }
                    }
                ?>
                </tbody>
            </table>
            <?php 
                if (isset($r) && is_array($r['products'])) {
            ?>
            <button class="btn btn-primary"> <a style="color:white;" href="user.php?confirm=" onclick="return confirm('CONFIRM?') ">CONFIRM</a></button>
            <?php 
                }
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>    
</body>
</html>