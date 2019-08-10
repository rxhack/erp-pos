<?php
    session_start();
    if(!isset($_SESSION['user'])) {
        header('location: ./');
    }

    include_once '../models/Product.php';
    
    $product = new Product();

    $r = null;

    if (isset($_POST['search'])) {

        switch ($_POST['type']) {
            case "all": {
                $r = $product->allProducts();
                break;
            }
            case "cid": {
                $r = $product->searchByCID($_POST['search']);
                break;
            }
            case "cname": {
                $r = $product->searchByCname($_POST['search']);
                break;
            }
            case "pcode": {
                $r = $product->searchByPcode($_POST['search']);
                break;
            }
            case "pname": {
                $r = $product->searchByPname($_POST['search']);
                break;
            }
            case "ptype": {
                $r = $product->searchByPtype($_POST['search']);
                break;
            }
            default:
                $r = array("pass"=>false, "msg"=>"Not a valid type!");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ERPOS</title>
</head>

<body>

    <div>
        <div style="text-align: center; padding: 2% 3%;">
            <form method="POST">
                <label for="type">Search By: </label>
                <select name="type" id="type">
                    <option value="all">All</option>
                    <option value="cid">Company ID</option>
                    <option value="cname">Company Name</option>
                    <option value="pcode">Product Code</option>
                    <option value="pname">Product Name</option>
                    <option value="ptype">Product Type</option>
                </select>
                <input type="text" name="search">
                <input type="submit" value="Search">
            </form>
        </div>

        <hr>

        <div style="padding: 5px;">
            <table border="1" style="width: 90%; margin: 0 auto;">
                <thead>
                    <tr>
                        <th>Company ID</th>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Product Type</th>
                        <th>Product Color</th>
                        <th>Product For</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (isset($_POST['search']) && $r['pass'] && $r['products'] != null) {
                            foreach ($r['products'] as $p) {
                    ?>
                        <tr>
                            <td><?php echo $p['cid']; ?></td>
                            <td><?php echo $p['pcode']; ?></td>
                            <td><?php echo $p['pname']; ?></td>
                            <td><?php echo $p['ptype']; ?></td>
                            <td><?php echo $p['pcolor']; ?></td>
                            <td><?php echo $p['pfor']; ?></td>
                            <td><?php echo $p['adderid']; ?></td>
                        </tr>
                    <?php
                            }
                        } else {
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: red; padding: 1.5% 0; font-weight: bold;"><?php  if($r == null){ echo "Search Product"; } else if (isset($_POST['search']) && !$r['pass']) { echo $r['msg']; } else if (isset($_POST['search']) && $r['products'] == null) { echo "No Product Found!"; } ?></td>
                    </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>