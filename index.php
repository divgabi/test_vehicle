<?php

class Vehicle {
    
  protected string $licence_number;
  protected string $type;
  protected int $year_in;

  public function __construct($conn, string $licence_number, ?string $type = null, ?int $year_in = 0) {
    $this->licence_number = $licence_number;
    $this->type = $type;
    $this->year_in = $year_in;

    $sql = "INSERT INTO vehicle (licence_number, type, year_in) VALUES ('$licence_number', '$type', $year_in)";

    if ($conn->query($sql) === TRUE) {
      echo "New vehicle created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function update($conn, string $licence_number, ?string $type = null, ?int $year_in = 0) {

    $sql = "UPDATE vehicle SET type = '$type', year_in = $year_in WHERE licence_number = '$licence_number'";
  
    if ($conn->query($sql) === TRUE) {
      echo "Vehicle updated successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function delete($conn, string $licence_number) {
    $sql = "DELETE FROM vehicle WHERE licence_number = '$licence_number'";
  
    if ($conn->query($sql) === TRUE) {
      echo "Vehicle deleted successfully";
    } else {
      echo "Error deleting vehicle: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function list($conn) {
    
    $sql = "SELECT
        *
      FROM
        vehicle
      ORDER BY
        licence_number
        ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "licence_number: " . $row["licence_number"]. " - Type: " . $row["type"]. "  - Year of putting into market: " . $row["year_in"]. PHP_EOL;
      }
    } else {
      echo "0 results";
    }
  }
}

$servername = "localhost";
$username = "user";
$password = "password";

/*$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully\n";*/

$dbname = "vehicles";

/*$sql = "CREATE DATABASE ".$dbname;
if ($conn->query($sql) === TRUE) {
  echo "Database created successfully\n";
} else {
  echo "Error creating database: " . $conn->error;
}*/

$conn = new mysqli($servername, $username, $password, $dbname);

/*$sql = "CREATE TABLE vehicle (
  licence_number VARCHAR(20) PRIMARY KEY,
  type VARCHAR(50),
  year_in INT(4)
)";
  
  if ($conn->query($sql) === TRUE) {
    echo "Table vehicle created successfully";
  } else {
    echo "Error creating table: " . $conn->error;
  }
*/

if (isset($argv)) {
  if (function_exists($argv[1])) {
    call_user_func($argv[1], isset($argv[2]) ? $argv[2] : null, isset($argv[3]) ? $argv[3] : null, isset($argv[4]) ? $argv[4] : null);
  }
}

function new_vehicle(string $licence_number, ?string $type = null, ?int $year_in = 0) {
  global $conn;
  $v = new Vehicle($conn, $licence_number, $type, $year_in);
}

function update_vehicle(string $licence_number, ?string $type = null, ?int $year_in = 0) {
  global $conn;
  Vehicle::update($conn, $licence_number, $type, $year_in);
}

function delete_vehicle(string $licence_number) {
  global $conn;
  Vehicle::delete($conn, $licence_number);
}

function list_vehicles() {
  global $conn;
  Vehicle::list($conn);
}

$conn->close();

?>