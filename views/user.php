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
        if (!isset($_SESSION['cid'])) {
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
<?php include_once 'template/header.php'; ?>

    <div class="container-fluid">
        <h3 class="text-dark mb-1"><strong>Add New Product</strong></h3>
        <?php
            if(isset($_GET['success'])) {
                echo '<div class="alert alert-success" role="alert">'.$_GET['success'].'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            } else if (isset($_GET['failed'])) {
                echo '<div class="alert alert-danger" role="alert">'.$_GET['failed'].'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            }
        ?>

        <!-- Start: new-product-add-form -->
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="adderid" value="<?php echo $_SESSION['user']; ?>" >
            <!-- Start: Form-Field -->
            <div class="form-field">
                <div class="form-row">
                    <div class="col">
                        <h4>Company Info</h4>
                        <hr>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-xl-3"><input type="text"name="cname"placeholder="enter company name" value="<?php echo $cname; ?>" class="form-control" required /></div>
                    <div class="col-xl-3"><input type="text"name="cid"placeholder="enter company ID" value="<?php echo $cid; ?>" class="form-control" required /></div>
                    <div class="col-xl-3"><input type="text"name="cgst"placeholder="enter company GST no" value="<?php echo $cgst; ?>" class="form-control" required /></div>
                    <div class="col-xl-3"><input class="form-control" type="text" placeholder="Phone Number"></div>
                </div>
                <div class="form-row">
                    <div class="col-xl-3"><input class="form-control" type="text" placeholder="Email Address"></div>
                    <div class="col-xl-3"><input class="form-control" type="text" placeholder="Location"></div>
                </div>
            </div>
            <!-- End: Form-Field -->
            <!-- Start: Form-Field -->
            <div class="form-field">
                <div class="form-row">
                    <div class="col">
                        <h4>Product Info</h4>
                        <hr>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-xl-3"><input type="text"name="pname"placeholder="enter product name" class="form-control" required /></div>
                    <div class="col-xl-3"><input type="text"name="pcolor"placeholder="enter product color" class="form-control" required /></div>
                    <div class="col-xl-3"><input type="text"name="ptype"placeholder="enter product type" class="form-control" required /></div>
                    <div class="col-xl-3"><input type="text"name="pfor"placeholder="enter product for" class="form-control" required /></div>
                </div>
                <div class="form-row">
                    <div class="col-xl-3"><input class="form-control" type="text" placeholder="Pattern"></div>
                    <div class="col-xl-3"><input class="form-control" type="text" placeholder="Weight"></div>
                    <div class="col-xl-3"><input class="form-control" type="text" placeholder="Batch No."></div>
                </div>
                <hr>
                <div class="form-row submit-row">
                    <div class="col-xl-2 offset-xl-10"><button class="btn btn-primary btn-block" type="submit" name="save"><strong>Submit</strong></button></div>
                </div>
            </div>
            <!-- End: Form-Field -->
        </form>
        
        <div class="table-field">
                <div class="form-group">
                    <div class="table-responsive data">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Company ID</th>
                                    <th>Product Name</th>
                                    <th>Product Code</th>
                                    <th>Product Color</th>
                                    <th>Product Type</th>
                                    <th>Product For</th>
                                    <th>Added By</th>
                                    <th>Action</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                   <?php     
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
                    <form method="POST">
                        <td><input type="hidden" name='id' value="<?php echo $pro['id']?>"/><input type="text"name="cid" value="<?php echo $pro["cid"];?>" class="form-control" disabled></td>
                        <td><input type="text"name="pname" value="<?php echo $pro["pname"];?>"class="form-control" required></td>
                        <td><input type="text"name="pcode"value="<?php echo $pro["pcode"];?>"class="form-control" required disabled></td>
                        <td><input type="text"name="pcolor" value="<?php echo $pro["pcolor"];?>"class="form-control" required></td>
                        <td><input type="text"name="ptype" value="<?php echo $pro["ptype"];?>"class="form-control" required></td>
                        <td><input type="text"name="pfor" value="<?php echo $pro["pfor"];?>"class="form-control" required></td>
                        <td><input type="text"name="pby" value="<?php echo $pro["adderid"];?>"class="form-control" disabled></td>
                        <td><button type="submit"name="update" class="btn btn-success">update</button></td>
                        <td><button class="btn btn-danger"><a style="color:White;"href="user.php?delete=<?php echo$pro["id"];?>"onclick="return confirm('Are you sure?')">Delete</a></button></td>
                    </form>
                    </tr>
                
                <?php
                                }
                            }
                        }
                    
                ?>
                
                            </tbody>
                        </table>
                    </div>
                    <div class="form-row submit-row">
                        <?php 
                            if (isset($r) && is_array($r['products'])) {
                        ?>
                           <div class="col-xl-2 offset-xl-10"> <button class="btn btn-primary btn-block"> <a style="color:white;" href="user.php?confirm=" onclick="return confirm('CONFIRM?') ">CONFIRM</a></button></div>
                        <?php 
                            }
                        ?> 
                    </div>
                </div>
        </div>
        </div>
        </div>
<?php include_once 'template/footer.php'; ?>       