<?php

use LDAP\Result;

include "db.php";
global $conn;

class welcome
{
    public static $greet = "=== Welcome to Sales Management System ===";
}

echo welcome::$greet;
echo "\n";
echo "\n";

echo "1. Product Management \n";
echo "2. Sales Module \n";
echo "3. Exit \n";

$manu = readline("Please select the menu\n");

if ($manu == 1) {
    echo "\n === PRODUCT MANAGEMENT === \n";
    echo "\n";
    echo "1. Add New Product \n";
    echo "2. Edit Existing Products \n";
    echo "3. Delete Product \n";
    echo "4. View All Products \n";
    echo "5. Show Low Stock Products \n";
    echo "6. Restock Products\n";

    $productManu = readline("Please select the option\n");
    if ($productManu == 1) {
        echo "\n === ADD PRODUCT === \n";
        $pId = readline("Enter ProductID: ");
        $pName = readline("Enter Name: ");
        $pCategory = readline("Enter Category: ");
        $pCost = readline("Enter Cost Price: ");
        $pPrice = readline("Enter Selling Price: ");
        $pQuantity = readline("Enter Quantity: ");
        echo "\n";
    }

    if ($productManu == 2) {
        echo "\n === EDIT EXISTING PRODUCTS === \n";
        $id = readline("Enter ProductID: ");

        if (isset($id)) {
            products::update_product($id);
        }
    }

    if ($productManu == 3) {
        echo "\n === DELETE A PRODUCT === \n";

        products::get_details();
        echo "\n";
        $delete_id = readline("Enter ProductID to Delete: ");

        if (isset($delete_id)) {
            products::delete_product($delete_id);
        }
    }

    if ($productManu == 4) {
        echo "\n === VIEW ALL PRODUCTS === \n";
        products::get_details();
    }

    if ($productManu == 5) {
        echo "\n === LOW STOCK PRODUCTS === \n";
        products::low_stock();
    }

    if ($productManu == 6) {
        echo "\n === RESTOCK PRODUCTS === \n";

        products::get_details();
        echo "\n";
        $restock_id = readline("Enter ProductID to Restock: ");
        $restock_qt = readline("Enter Restock Quantity: ");

        if (isset($restock_id)) {
            products::restock_product($restock_id, $restock_qt);
        }
    }
}

if ($manu == 2) {
    echo "\n === SALES MODULE === \n";
    echo "ENTER BUYER'S DETAILS:";

    $customerName = readline("\nEnter customer's name: \n");

    if (isset($customerName)) {
        echo "\n What " . $customerName . " wants to buy? \n";
        products::get_details();

        $pId = readline("\nSelect a Product By ProductID: ");
        $pQuantity = readline("\nEnter Quantity: ");
        $pDiscount = readline("Enter the Discount: ");
        $pTax = readline("Enter the Tax: ");

        customers::addCustomer($pId, $pQuantity, $customerName, $pDiscount, $pTax);
    }
}

if ($manu == 3) {
    exit;
}

class products
{
    public $productId;
    public $productName;
    public $productCategory;
    public $productCost;
    public $productPrice;
    public $productQuantity;

    public function __construct($productId, $productName, $productCategory, $productCost, $productPrice, $productQuantity)
    {
        global $conn;

        $pId = $this->productId = $productId;
        $pName = $this->productName = $productName;
        $pCategory =  $this->productCategory = $productCategory;
        $pCost = $this->productCost = $productCost;
        $pPrice = $this->productPrice = $productPrice;
        $pQuantity = $this->productQuantity = $productQuantity;

        $sql = "INSERT INTO products (pId, pName, pCatagory, pCost, pPrice, pQuantity) VALUES($pId, '$pName', '$pCategory', $pCost, $pPrice, $pQuantity)";
        if (mysqli_query($conn, $sql)) {
            echo "Product Sucessfully Added!";
        }
    }



    public static function get_details()
    {
        global $conn;
        $sql = "SELECT * FROM products";
        $result = mysqli_query($conn, $sql);

        while ($row = $result->fetch_assoc()) {
            echo "\n PRODUCT DETAILS \n";
            echo "pId: " . $row['pId'] . "\n";
            echo "Name: " . $row['pName'] . "\n";
            echo "Category: " . $row['pCatagory'] . "\n";
            echo "Cost: " . $row['pCost'] . "\n";
            echo "Selling Price: " . $row['pPrice'] . "\n";
            echo "Quantity: " . $row['pQuantity'] . "\n";
        }
    }

    public static function update_product($id)
    {
        global $conn;
        $sql1 = "SELECT pName, pCatagory, pCost, pPrice, pQuantity FROM products WHERE pId = $id";
        $result = mysqli_query($conn, $sql1);

        while ($row = $result->fetch_assoc()) {
            echo "\n PRODUCT DETAILS \n";

            $pName = readline("\n Old Name: " . $row['pName'] . " | Enter New Value: ");
            if (empty($pName)) {
                $pName = $row['pName'];
            }
            $pCatagory = readline("\n Old Category: " . $row['pCatagory'] . " | Enter New Value: ");
            if (empty($pCatagory)) {
                $pCatagory = $row['pCatagory'];
            }
            $pCost = readline("\n Old Cost: " . $row['pCost'] . " | Enter New Value: ");
            if (empty($pCost)) {
                $pCost = $row['pCost'];
            }
            $pPrice = readline("\n Old Price: " . $row['pPrice'] . " | Enter New Value: ");
            if (empty($pPrice)) {
                $pPrice = $row['pPrice'];
            }
            $pQuantity = readline("\n Old Quantity: " . $row['pQuantity'] . " | Enter New Value: ");
            if (empty($pQuantity)) {
                $pQuantity = $row['pQuantity'];
            }

            $sql2 = "UPDATE products SET pName = '$pName', pCatagory = '$pCatagory', pCost = $pCost, pPrice = '$pPrice', pQuantity = '$pQuantity' WHERE pId = $id";
            if (mysqli_query($conn, $sql2)) {
                echo "\n Product Successfully Updated";
            }
        }
    }

    public static function delete_product($id)
    {
        global $conn;
        $sql = "DELETE FROM products WHERE pId = $id";
        if (mysqli_query($conn, $sql)) {
            echo "Product Successfully Deleted!";
        }
    }

    public static function low_stock()
    {
        global $conn;
        $sql = "SELECT * FROM products WHERE pQuantity < 100";
        $result = mysqli_query($conn, $sql);

        while ($row = $result->fetch_assoc()) {
            echo "\n PRODUCT DETAILS \n";
            echo "pId: " . $row['pId'] . "\n";
            echo "Name: " . $row['pName'] . "\n";
            echo "Category: " . $row['pCatagory'] . "\n";
            echo "Cost: " . $row['pCost'] . "\n";
            echo "Selling Price: " . $row['pPrice'] . "\n";
            echo "Quantity: " . $row['pQuantity'] . "\n";
        }
    }

    public static function restock_product($id, $pQuantity)
    {
        global $conn;
        $sql = "UPDATE products SET pQuantity = pQuantity + $pQuantity WHERE pId = $id";
        if (mysqli_query($conn, $sql)) {
            echo "Product Successfully Restocked!";
        }
    }
}

if (isset($pId) && isset($pName) && isset($pCategory) && isset($pCost) && isset($pPrice) && isset($pQuantity)) {
    $product2 = new products($pId, $pName, $pCategory, $pCost, $pPrice, $pQuantity);
}

class customers
{
    public static $redirectManu;
    public static $mainList;

    static function addCustomer($pid, $pQuantity, $customerName, $discount, $tax)
    {
        global $conn;

        $sql1 = "SELECT pPrice AS price, pQuantity AS quantity, pName as name FROM products WHERE pId = $pid";
        $result = mysqli_query($conn, $sql1)->fetch_assoc();
        $price = $result['price'];
        $quantity = $result['quantity'];
        $name = $result['name'];
        $upd_quantity = $quantity - $pQuantity;
        $total_cost = $price * $pQuantity;
        $discountedPrice = ($price * $discount) / 100;
        $taxOnPrice = ($price * $tax) / 100;
        $final_bill = ($total_cost + $taxOnPrice) - $discountedPrice;


        $sql = "INSERT INTO customers (name, total_cost, discount, tax, final_bill) VALUES('$customerName', $total_cost, $discount, $tax, $final_bill)";
        if (mysqli_query($conn, $sql)) {
            echo "Order Successfully Confirmed! \n";
            echo "\n";
            echo "INVOICE: \n";
            echo "Name: " . $customerName . "\n";
            echo "Product: " . $name . "\n";
            echo "Quantity: *" . $pQuantity . "\n";
            echo "Price: " . $price . "\n";
            echo "Discount: %" . $discount . "\n";
            echo "Tax: %" . $tax . "\n";
            echo "Final Bill: " . $final_bill . "\n";
        }

        $sql1 = "UPDATE products SET pQuantity = $upd_quantity WHERE pId = $pid";
        if(mysqli_query($conn, $sql1)){
            self::$redirectManu = readline("\n Press 0 for manu: ");
            if(isset(self::$redirectManu)){
                self::$mainList = 0; 
            }
        }
    }
}
