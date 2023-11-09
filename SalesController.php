<?php

class SalesController {

    public function index()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM `sales_tbl`");
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($sales);
    }

    public function store($params) {
        global $pdo;

        if (!isset($params['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $id = $params['id'];

        // Validate that $id is a positive integer
        if (!ctype_digit((string)$id) || $id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['qty'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Quantity is required from the body']);
            return;
        }

        // Validate that $id is a positive integer
        if (!ctype_digit((string)$data['qty']) || $data['qty'] <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid quantity.']);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM `products_tbl` WHERE `id` = ?");
        $stmt->execute([$id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        if($data['qty'] > $product['qty']) {
            http_response_code(200);
            echo json_encode([
                'error' => 'Withdrawn quantity is higher than current quantity.',
                'qty' => $product['qty']
            ]);
            return;
        }

        $total_amount = $product["price"] * $data['qty'];

        $stmt = $pdo->prepare("UPDATE `products_tbl` SET `qty` = `qty` - ? WHERE `id` = ?");
        $stmt->execute([$data["qty"], $id]);

        $stmt = $pdo->prepare("INSERT INTO `sales_tbl` (`product_id`, `qty`, `total_amount`) VALUES (?, ?, ?)");
        $stmt->execute([$id, $data["qty"], $total_amount]);

        echo json_encode([
            "message" => "Sales successfully executed!",
            "amount" => $total_amount
        ]);
    }

    public function withdraw() {
        global $pdo;

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $id = $data['id'];

        // Validate that $id is a positive integer
        if (!ctype_digit((string)$id) || $id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        if (!isset($data['qty'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Quantity is required from the body']);
            return;
        }

        $qty = $data['qty'];

        // Validate that $id is a positive integer
        if (!ctype_digit((string)$qty) || $qty <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid quantity.']);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM `products_tbl` WHERE `id` = ?");
        $stmt->execute([$id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        if($qty > $product['qty']) {
            http_response_code(200);
            echo json_encode([
                'error' => 'Withdrawn quantity is higher than current quantity.',
                'qty' => $product['qty']
            ]);
            return;
        }

        $total_amount = $product["price"] * $qty;

        $stmt = $pdo->prepare("UPDATE `products_tbl` SET `qty` = `qty` - ? WHERE `id` = ?");
        $stmt->execute([$qty, $id]);

        $stmt = $pdo->prepare("INSERT INTO `sales_tbl` (`product_id`, `qty`, `total_amount`) VALUES (?, ?, ?)");
        $stmt->execute([$id, $qty, $total_amount]);

        echo json_encode([
            "message" => "Sales successfully executed!",
            "amount" => $total_amount
        ]);
    }
    

    public function test($params) {
        global $pdo;

        $inputs = json_decode(file_get_contents("php://input"), true);

        if (!isset($params['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $id = $params['id'];

        // Validate that $id is a positive integer
        if (!ctype_digit((string)$id) || $id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }
        
        // CURL HERE 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/itec116_api/products/' . $id);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result);

        if (!isset($inputs['qty'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Quantity is required from the body']);
            return;
        }

        $qty = $inputs['qty'];

        // Validate that $id is a positive integer
        if (!ctype_digit((string)$qty) || $qty <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid quantity.']);
            return;
        }

        if($qty > $data->qty) {
            http_response_code(200);
            echo json_encode([
                'error' => 'Withdrawn quantity is higher than current quantity.',
                'qty' => $data->qty
            ]);
            return;
        }

        $total_amount = $data->price * $qty;

        $stmt = $pdo->prepare("UPDATE `products_tbl` SET `qty` = `qty` - ? WHERE `id` = ?");
        $stmt->execute([$qty, $id]);
        

        $stmt = $pdo->prepare("INSERT INTO `sales_tbl` (`product_id`, `qty`, `total_amount`) VALUES (?, ?, ?)");
        $stmt->execute([$id, $qty, $total_amount]);

        echo json_encode([
            "message" => "Sales successfully executed!",
            "amount" => $total_amount
        ]);
    }

    public function salesReportPerItem($params) {
        global $pdo;

        if (!isset($params['product_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $product_id = $params['product_id'];

        // Validate that $id is a positive integer
        if (!ctype_digit((string)$product_id) || $product_id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM `products_tbl` WHERE `id` = ?");
        $stmt->execute([$product_id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        $stmt = $pdo->prepare("SELECT `p`.`id` `product_id`, `p`.`name` `product_name`, `p`.`category` `product_category`, COUNT(`s`.`id`) `sales_count`, SUM(`s`.`qty`) `sales_qty`, SUM(`total_amount`) `sales_amount` FROM `sales_tbl` `s` LEFT JOIN `products_tbl` `p` ON `p`.`id` = `s`.`product_id` WHERE `product_id` = ? GROUP BY `product_id`");
        $stmt->execute([$product_id]);

        $sales = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sales) {
            http_response_code(404);
            echo json_encode(['error' => 'No sales found']);
            return;
        }

        echo json_encode([
            "message" => "Sales report successfully generated!",
            "data" => $sales
        ]);
    }
}