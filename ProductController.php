<?php

class ProductController
{
    public function index()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM `products_tbl`");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
    }

    public function store()
    {
        global $pdo;

        $data = json_decode(file_get_contents("php://input"), true);

        $stmt = $pdo->prepare("INSERT INTO `products_tbl` (`name`, `category`, `price`, `qty`) 
                              VALUES (?, ?, ?, ?)");

        $stmt->execute([$data["name"], $data["category"], $data["price"], $data["qty"]]);

        echo json_encode(["message" => "Product successfully added!"]);
    }
    
    public function update($params)
    {
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

        // Validate that required fields are present in the request
        if (empty($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request. Request body is empty.']);
            return;
        }

        $setClauses = [];
        $paramsToBind = [];

        foreach ($data as $key => $value) {
            // Only include valid fields that can be updated
            $allowedFields = ['qty'];
            if (in_array($key, $allowedFields)) {
                $setClauses[] = "$key = ?";
                $paramsToBind[] = $value;
            }
        }

        // If no valid fields are found for update
        if (empty($setClauses)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request. No valid fields provided for update.']);
            return;
        }

        // Prepare the SQL query dynamically based on the fields to update
        $sql = "UPDATE `products_tbl` SET " . implode(', ', $setClauses) . " WHERE `id` = ?";
        $paramsToBind[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($paramsToBind);

        echo json_encode(['message' => 'Product successfully updated!']);
    }
    
    public function show($params)
    {
        global $pdo;

        // Check if 'id' is present in the parameters
        if (!isset($params['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $id = $params['id'];

        // Validate that $id is a positive integer
        if (!ctype_digit((string)$id) || $id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid product ID']);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM `products_tbl` WHERE id = ?");
        $stmt->execute([$id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            return;
        }

        echo json_encode($product);
    }
}

?>