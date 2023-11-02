<?php

class ExpenseController
{
    public function index()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM expenses_tbl");
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($cars);
    }

    public function store()
    {
        global $pdo;

        $data = json_decode(file_get_contents("php://input"), true);

        $stmt = $pdo->prepare("INSERT INTO expenses_tbl (name, amount, month, week) 
                              VALUES (?, ?, ?, ?)");

        $stmt->execute([$data["name"], $data["amount"], $data["month"], $data["week"]]);

        echo json_encode(["message" => "Expenses added successfully"]);
    }

    public function show($params) {
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
            echo json_encode(['error' => 'Invalid expense ID']);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM expenses_tbl WHERE id = ?");
        $stmt->execute([$id]);

        $expense = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$expense) {
            http_response_code(404);
            echo json_encode(['error' => 'Expense not found']);
            return;
        }

        echo json_encode($expense);
    }

    public function destroy($params) {
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
            echo json_encode(['error' => 'Invalid expense ID']);
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM expenses_tbl WHERE id = ?");
        $stmt->execute([$id]);
        
        $expense = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$expense) {
            http_response_code(404);
            echo json_encode(['error' => 'Expense not found']);
            return;
        }

        $stmt = $pdo->prepare("DELETE FROM expenses_tbl WHERE id = ?");
        $stmt->execute([$id]);

        $expense = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['message' => "Successfully deleted."]);
    }
}

?>