<?php
class Database {

	private $host;
	private $username;
	private $password;
	private $database;
	private $con;

	/**
	 * @param String $host
	 * @param String $user
	 * @param String $pass
	 * @param String $db
	 * @param String $table
	 */
	public function __construct($host, $user, $pass, $db) {
		$this->host 	= $host;
		$this->username = $user;
		$this->password = $pass;
		$this->database = $db;
	}

	public function connect() {
		try {
			$this->con = new PDO('mysql:host='.$this->host.';dbname='.$this->database.';charset=utf8', $this->username, $this->password);
			$this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			$this->con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return true;
		} catch (Exception $e) {
			echo 'Invalid database credentials!';
			return false;
		}
	}

	public function getProducts($cat) {
		$stmt = $this->con->prepare("SELECT * FROM products WHERE category=:cat");
		$stmt->execute(array("cat" => $cat));
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getAllProducts() {
		$stmt = $this->con->prepare("SELECT * FROM products");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getProduct($item_id) {
		$stmt = $this->con->prepare("SELECT * FROM products WHERE item_id=:id");
		$stmt->execute(array("id" => $item_id));
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function deleteProduct($item_id) {
		$stmt = $this->con->prepare("DELETE FROM products WHERE item_id=:id");
		$stmt->execute(array("id" => $item_id));
	}

	public function deleteCategory($cid) {
		$stmt = $this->con->prepare("DELETE FROM categories WHERE cid=:cid");
		$stmt->bindParam(":cid", $cid);
		$stmt->execute();
	}

	public function getCategories() {
		$stmt = $this->con->prepare("SELECT * FROM categories ORDER BY zindex ASC");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function getCategory($id) {
		$stmt = $this->con->prepare("SELECT * FROM categories WHERE cid=:id LIMIT 1");
		$stmt->bindParam(":id", $id);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function addPayment($item_name, $item_number, $status, $amount, $quantity, $currency, $buyer, $receiver, $playername) {
		$stmt = $this->con->prepare("INSERT INTO payments (item_name, item_number, status, amount, quantity, currency, buyer, receiver, player_name) VALUES (:item_name, :item_number, :status, :amount, :quantity, :currency, :buyer, :receiver, :playername)");
		$stmt->bindParam(":item_name", $item_name);
		$stmt->bindParam(":item_number", $item_number);
		$stmt->bindParam(":status", $status);
		$stmt->bindParam(":amount", $amount);
		$stmt->bindParam(":quantity", $quantity);
		$stmt->bindParam(":currency", $currency);
		$stmt->bindParam(":buyer", $buyer);
		$stmt->bindParam(":receiver", $receiver);
		$stmt->bindParam(":playername", $playername);
		$stmt->execute();
	}

	public function addHash($hash) {
		$stmt = $this->con->prepare("INSERT INTO used_hashes (hash) VALUES(:hash)");
		$stmt->bindParam(":hash", $hash);
		$stmt->execute();
	}

	public function getHash($hash) {
		$stmt = $this->con->prepare("SELECT * FROM used_hashes WHERE hash=:hash");
		$stmt->bindParam(":hash", $hash);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function getAllPayments() {
		$stmt = $this->con->prepare("SELECT * FROM payments ORDER BY dateline DESC LIMIT 50");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getPayments2($start) {
		$stmt = $this->con->prepare("SELECT * FROM payments ORDER BY dateline DESC LIMIT $start, 50");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getPayments($username) {
		$stmt = $this->con->prepare("SELECT * FROM payments WHERE player_name=:name AND status='Completed'");
		$stmt->bindParam(":name", $username);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function setClaimed($id) {
		$stmt = $this->con->prepare("UPDATE payments SET claimed=1 WHERE id=:id");
		$stmt->bindParam(":id", $id);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function countProductsInCat($cat) {
		$stmt = $this->con->prepare("SELECT COUNT(*) FROM products WHERE category=:cat");
		$stmt->execute(array("cat" => $cat));
		return $stmt->fetchColumn();
	}

	public function insert($table, $vars) {
        $keys = array_keys($vars);
        $query = "INSERT INTO $table (";
        for ($i = 0; $i < count($keys); $i++) {
            $query .= ''.$keys[$i].($i < count($keys) - 1 ? ", " : ") VALUES (");
        }
        for ($i = 0; $i < count($keys); $i++) {
            $query .= ':'.$keys[$i].($i < count($keys) - 1 ? ", " : ")");
        }
        $stmt = $this->con->prepare($query);
        $stmt->execute($vars);
    }

    public function update($table, $key, $vars) {
    	$keys = array_keys($vars);
    	$query = "UPDATE $table SET ";
    	for ($i = 0; $i < count($keys); $i++) {
    		 $query .= "".$keys[$i]."=:".$keys[$i]."".($i < count($keys) - 1 ? ", " : "");
    	}
    	$query .= " WHERE item_id=$key";
    	$stmt = $this->con->prepare($query);
        $stmt->execute($vars);
    }
}
?>
