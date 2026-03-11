<?php
class Database {

    private $host;
    private $username;
    private $password;
    private $database;
    private $con;

    public function __construct($host, $user, $pass, $db) {
        $this->host     = $host;
        $this->username = $user;
        $this->password = $pass;
        $this->database = $db;
    }

    public function connect() {
        try {
            $this->con = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=utf8',
                $this->username,
                $this->password
            );
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return true;
        } catch (Exception $e) {
            echo 'Invalid database credentials!';
            return false;
        }
    }

    /**
     * Current serverdb does not have categories.
     * Return all enabled products instead.
     */
    public function getProducts($cat) {
        return $this->getAllProducts();
    }

    /**
     * Alias price -> item_price and hardcode discount to 0
     * so the existing templates/process flow still works.
     */
public function getAllProducts() {
    $stmt = $this->con->prepare("
        SELECT
            id,
            item_id,
            item_name,
            description,
            image_url,
            price AS item_price,
            0 AS item_discount,
            enabled,
            sort_order
        FROM products
        WHERE enabled = 1
        ORDER BY sort_order ASC, id ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getProduct($item_id) {
    $stmt = $this->con->prepare("
        SELECT
            id,
            item_id,
            item_name,
            description,
            image_url,
            price AS item_price,
            0 AS item_discount,
            enabled,
            sort_order
        FROM products
        WHERE item_id = :id
        LIMIT 1
    ");
    $stmt->execute(array("id" => $item_id));
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function deleteProduct($item_id) {
        $stmt = $this->con->prepare("DELETE FROM products WHERE item_id = :id");
        $stmt->execute(array("id" => $item_id));
    }

    public function deleteCategory($cid) {
        // No-op because your current schema has no categories table.
        return true;
    }

    public function getCategories() {
        // Return empty list so index.php won't explode.
        return array();
    }

    public function getCategory($id) {
        return null;
    }

    public function addHash($hash) {
        $stmt = $this->con->prepare("INSERT INTO used_hashes (hash) VALUES (:hash)");
        $stmt->bindParam(":hash", $hash);
        $stmt->execute();
    }

    public function getHash($hash) {
        $stmt = $this->con->prepare("SELECT * FROM used_hashes WHERE hash = :hash LIMIT 1");
        $stmt->bindParam(":hash", $hash);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllPayments() {
        $stmt = $this->con->prepare("SELECT * FROM payments ORDER BY id DESC LIMIT 50");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPayments2($start) {
        $start = (int)$start;
        $stmt = $this->con->prepare("SELECT * FROM payments ORDER BY id DESC LIMIT $start, 50");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Debug/helper endpoint only.
     * The game should claim directly from the DB now.
     */
    public function getPayments($username) {
        $stmt = $this->con->prepare("
            SELECT *
            FROM payments
            WHERE username = :name
              AND status = 'completed'
              AND store = 'Grimoire'
              AND claimed_datetime IS NULL
            ORDER BY id ASC
        ");
        $stmt->bindParam(":name", $username);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setClaimed($id) {
        $stmt = $this->con->prepare("
            UPDATE payments
            SET status = 'claimed',
                claimed_datetime = NOW()
            WHERE id = :id
        ");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return true;
    }

    public function countProductsInCat($cat) {
        // No categories in current schema.
        return 0;
    }

    public function paymentExists($invoiceId, $username, $itemId) {
        $stmt = $this->con->prepare("
            SELECT id
            FROM payments
            WHERE invoice_id = :invoice_id
              AND username = :username
              AND item_id = :item_id
            LIMIT 1
        ");
        $stmt->execute(array(
            "invoice_id" => $invoiceId,
            "username"   => $username,
            "item_id"    => $itemId
        ));
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function insertPayment($data) {
        $stmt = $this->con->prepare("
            INSERT INTO payments (
                username,
                product_name,
                item_id,
                item_quantity,
                price,
                invoice_id,
                purchase_datetime,
                claimed_datetime,
                ip_address,
                serial_address,
                status,
                store
            ) VALUES (
                :username,
                :product_name,
                :item_id,
                :item_quantity,
                :price,
                :invoice_id,
                :purchase_datetime,
                :claimed_datetime,
                :ip_address,
                :serial_address,
                :status,
                :store
            )
        ");
        $stmt->execute($data);
    }

    public function insert($table, $vars) {
        $keys = array_keys($vars);
        $query = "INSERT INTO $table (";
        for ($i = 0; $i < count($keys); $i++) {
            $query .= $keys[$i] . ($i < count($keys) - 1 ? ", " : ") VALUES (");
        }
        for ($i = 0; $i < count($keys); $i++) {
            $query .= ':' . $keys[$i] . ($i < count($keys) - 1 ? ", " : ")");
        }
        $stmt = $this->con->prepare($query);
        $stmt->execute($vars);
    }

    public function update($table, $key, $vars) {
        $keys = array_keys($vars);
        $query = "UPDATE $table SET ";
        for ($i = 0; $i < count($keys); $i++) {
            $query .= $keys[$i] . "=:" . $keys[$i] . ($i < count($keys) - 1 ? ", " : "");
        }
        $query .= " WHERE item_id = $key";
        $stmt = $this->con->prepare($query);
        $stmt->execute($vars);
    }
}
?>