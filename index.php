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

  public static function new_vehicle_usage($conn, int $driver_id, string $licence_number, string $date) {

    $sql = "INSERT INTO vehicle_usage (driver_id, vehicle_licence_number, usage_date) VALUES ($driver_id, '$licence_number', '$date');";

    if ($conn->query($sql) === TRUE) {
      echo "New vehicle_usage created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function list_vehicle_usage($conn) {

    $sql = "SELECT
        CONCAT(vehicle.licence_number, ' ', vehicle.type, ' ', vehicle.year_in) AS v,
        CONCAT(driver.name, ' ', driver.birth_year) AS d,
        vehicle_usage.usage_date AS 'day'
      FROM
        vehicle
        INNER JOIN vehicle_usage ON vehicle.licence_number = vehicle_usage.vehicle_licence_number
        INNER JOIN driver ON vehicle_usage.driver_id = driver.id
      ORDER BY
        vehicle.licence_number,
        driver.id,
        vehicle_usage.usage_date
        ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "vehicle: " . $row["v"]. " - driver: " . $row["d"]. "  - Day: " . $row["day"]. PHP_EOL;
      }
    } else {
      echo "0 results";
    }
  }
}

class Driver {
  protected int $id;
  protected string $name;
  protected int $birth_year;

  public function __construct($conn, string $name, int $birth_year) {
    $this->name = $name;
    $this->birth_year = $birth_year;

    $sql = "INSERT INTO driver (name, birth_year) VALUES ('$name', $birth_year)";

    if ($conn->query($sql) === TRUE) {
      echo "New driver created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function update($conn, int $id, string $name, int $birth_year) {

    $sql = "UPDATE driver SET name = '$name', birth_year = $birth_year WHERE id = $id";
  
    if ($conn->query($sql) === TRUE) {
      echo "Driver updated successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function delete($conn, string $id) {
    $sql = "DELETE FROM driver WHERE id = '$id'";
  
    if ($conn->query($sql) === TRUE) {
      echo "Driver deleted successfully";
    } else {
      echo "Error deleting driver: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function list($conn) {
    
    $sql = "SELECT
        *
      FROM
        driver
      ORDER BY
        name
        ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Birth year: " . $row["birth_year"]. PHP_EOL;
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

if (isset($argv) && isset($argv[1])) {
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

function new_driver(string $name, int $birth_year) {
  global $conn;
  $d = new Driver($conn, $name, $birth_year);
}

function update_driver(int $id, string $name, int $birth_year) {
  global $conn;
  Driver::update($conn, $id, $name, $birth_year);
}

function delete_driver(string $id) {
  global $conn;
  Driver::delete($conn, $id);
}

function list_drivers() {
  global $conn;
  Driver::list($conn);
}

function new_vehicle_usage(int $driver_id, string $licence_number, string $date) {
  global $conn;
  Vehicle::new_vehicle_usage($conn, $driver_id, $licence_number, $date);
}

function list_vehicle_usage() {
  global $conn;
  Vehicle::list_vehicle_usage($conn);
}

$conn->close();

?>