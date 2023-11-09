<?php

class CarController
{
    public function index()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM cars");
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($cars);
    }

    public function store()
    {
        global $pdo;
        $data = json_decode(file_get_contents("php://input"), true);

        $stmt = $pdo->prepare("INSERT INTO cars (make, model, year, price, color, mileage, engine_type, transmission_type) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([$data["make"], $data["model"], $data["year"], $data["price"], 
                        $data["color"], $data["mileage"], $data["engine_type"], $data["transmission_type"]]);

        echo json_encode(["message" => "Car added successfully"]);
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
            echo json_encode(['error' => 'Invalid car ID']);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
        $stmt->execute([$id]);

        $car = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$car) {
            http_response_code(404);
            echo json_encode(['error' => 'Car not found']);
            return;
        }

        echo json_encode($car);
    }
    
    /**
     * Update details of a specific car by ID
     */
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
            echo json_encode(['error' => 'Invalid car ID']);
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
            $allowedFields = ['make', 'model', 'year', 'price', 'color', 'mileage', 'engine_type', 'transmission_type'];
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
        $sql = "UPDATE cars SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $paramsToBind[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($paramsToBind);

        echo json_encode(['message' => 'Car updated successfully']);
    }

    /**
     * Delete a specific car by ID
     */
    public function destroy($params)
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
            echo json_encode(['error' => 'Invalid car ID']);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['message' => 'Car deleted successfully']);
    }
}

?>