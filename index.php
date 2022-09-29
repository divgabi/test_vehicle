<?php

class Vehicle {
    
  protected string $licence_number;
  protected string $type;
  protected int $year_in;

  public function __construct($conn, string $licence_number, string $type, int $year_in) {
    
    $this->licence_number = $licence_number;
    $this->type = $type;
    $this->year_in = $year_in;

    $sql = "INSERT INTO vehicle (licence_number, type, year_in) VALUES ('$licence_number', '$type', $year_in)";

    if ($conn->query($sql) === TRUE) {
      echo "New vehicle created successfully". PHP_EOL;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public function update($conn, string $licence_number, string $type, int $year_in) {

    $sql = "UPDATE vehicle SET type = '$type', year_in = $year_in WHERE licence_number = '$licence_number'";
  
    if ($conn->query($sql) === TRUE) {
      echo "Vehicle updated successfully". PHP_EOL;
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
        vehicle.*,
        CASE
          WHEN car.licence_number IS NOT NULL THEN ' (car.number_of_persons_that_can_be_transported)'
          WHEN pickup.licence_number IS NOT NULL THEN ' (pickup.transportable_load)'
          WHEN truck.licence_number IS NOT NULL THEN ' (truck.transportable_load)'
          ELSE NULL
        END AS brackets
      FROM
        vehicle
        LEFT JOIN car ON vehicle.licence_number = car.licence_number
        LEFT JOIN pickup ON vehicle.licence_number = pickup.licence_number
        LEFT JOIN truck ON vehicle.licence_number = truck.licence_number
      ORDER BY
        licence_number
        ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "licence_number: " . $row["licence_number"]. " - Type: " . $row["type"]. "  - Year of putting into market: " . $row["year_in"] . $row["brackets"]. PHP_EOL;
      }
    } else {
      echo "0 results";
    }
  }

  public static function new_vehicle_usage($conn, int $driver_id, string $licence_number, string $date) {

    $sql = "SELECT * FROM driver WHERE id = $driver_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $d_license_type = $row['license_type'];
    } else {
      echo "No such driver!" . PHP_EOL;
      exit();
    }
    $sql = "SELECT
        COALESCE(car.drive_with, pickup.drive_with, truck.drive_with) AS drive_with_licence
      FROM
        vehicle
        LEFT JOIN car ON vehicle.licence_number = car.licence_number
        LEFT JOIN pickup ON vehicle.licence_number = pickup.licence_number
        LEFT JOIN truck ON vehicle.licence_number = truck.licence_number
      WHERE
        vehicle.licence_number = '$licence_number'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $v_licence = $row['drive_with_licence'];
    } else {
      echo "No such vehicle!" . PHP_EOL;
      exit();
    }
    //echo $d_license_type. PHP_EOL.$v_licence.PHP_EOL.strpos($d_license_type, $v_licence).PHP_EOL;

    if (strpos($v_licence, $d_license_type) !== false) {

      $sql = "INSERT INTO vehicle_usage (driver_id, vehicle_licence_number, usage_date) VALUES ($driver_id, '$licence_number', '$date');";

      if ($conn->query($sql) === TRUE) {
        echo "New vehicle_usage created successfully" . PHP_EOL;
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
    } else {
      echo "New vehicle_usage not created: driver has no right license type" . PHP_EOL;
    }
  }

  public static function list_vehicle_usage($conn, $from, $to) {

    $sql = "SELECT
        CONCAT(vehicle.licence_number, ' ', vehicle.type, ' ', vehicle.year_in,
        CASE
          WHEN car.licence_number IS NOT NULL THEN CONCAT(' (', car.number_of_persons_that_can_be_transported, ' persons)')
          WHEN pickup.licence_number IS NOT NULL THEN CONCAT(' (', pickup.transportable_load, ' kg)')
          WHEN truck.licence_number IS NOT NULL THEN CONCAT(' (', truck.transportable_load, ' kg)')
          ELSE NULL
        END
        ) AS v,
        CONCAT(driver.name, ' ', driver.birth_year) AS d,
        vehicle_usage.usage_date AS 'day'
      FROM
        vehicle
        INNER JOIN vehicle_usage ON vehicle.licence_number = vehicle_usage.vehicle_licence_number
        INNER JOIN driver ON vehicle_usage.driver_id = driver.id
        LEFT JOIN car ON vehicle.licence_number = car.licence_number
        LEFT JOIN pickup ON vehicle.licence_number = pickup.licence_number
        LEFT JOIN truck ON vehicle.licence_number = truck.licence_number
      WHERE
        vehicle_usage.usage_date >= '$from'
        AND vehicle_usage.usage_date <= '$to'
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

class Car extends Vehicle {
  protected int $number_of_persons_that_can_be_transported;
  const drive_with = "BCD";

  public function __construct($conn, string $licence_number, string $type, int $year_in, int $number_of_persons_that_can_be_transported) {
    
    parent::__construct($conn, $licence_number, $type, $year_in);

    $this->number_of_persons_that_can_be_transported = $number_of_persons_that_can_be_transported;

    $sql = "INSERT INTO car (licence_number, number_of_persons_that_can_be_transported) VALUES ('$licence_number', $number_of_persons_that_can_be_transported)";

    if ($conn->query($sql) === TRUE) {
      echo "New car created successfully". PHP_EOL;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function update_car($conn, string $licence_number, string $type, int $year_in, int $number_of_persons_that_can_be_transported) {

    parent::update($conn, $licence_number, $type, $year_in);

    $sql = "UPDATE car SET number_of_persons_that_can_be_transported = $number_of_persons_that_can_be_transported WHERE licence_number = '$licence_number'";
  
    if ($conn->query($sql) === TRUE) {
      echo "Car updated successfully". PHP_EOL;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
}

class Pickup extends Vehicle {
  protected int $transportable_load;
  const drive_with = "CD";

  public function __construct($conn, string $licence_number, string $type, int $year_in, int $transportable_load) {
    
    parent::__construct($conn, $licence_number, $type, $year_in);

    $this->transportable_load = $transportable_load;

    $sql = "INSERT INTO pickup (licence_number, transportable_load) VALUES ('$licence_number', $transportable_load)";

    if ($conn->query($sql) === TRUE) {
      echo "New pickup created successfully". PHP_EOL;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function update_pickup($conn, string $licence_number, string $type, int $year_in, int $transportable_load) {

    parent::update($conn, $licence_number, $type, $year_in);

    $sql = "UPDATE pickup SET transportable_load = $transportable_load WHERE licence_number = '$licence_number'";
  
    if ($conn->query($sql) === TRUE) {
      echo "Pickup updated successfully". PHP_EOL;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
}

class Truck extends Vehicle {
  protected int $transportable_load;
  const drive_with = "D";

  public function __construct($conn, string $licence_number, string $type, int $year_in, int $transportable_load) {
    
    parent::__construct($conn, $licence_number, $type, $year_in);

    $this->transportable_load = $transportable_load;

    $sql = "INSERT INTO truck (licence_number, transportable_load) VALUES ('$licence_number', $transportable_load)";

    if ($conn->query($sql) === TRUE) {
      echo "New pickup created successfully". PHP_EOL;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function update_truck($conn, string $licence_number, string $type, int $year_in, int $transportable_load) {

    parent::update($conn, $licence_number, $type, $year_in);

    $sql = "UPDATE pickup SET transportable_load = $transportable_load WHERE licence_number = '$licence_number'";
  
    if ($conn->query($sql) === TRUE) {
      echo "Pickup updated successfully". PHP_EOL;
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
}

class Driver {
  protected int $id;
  protected string $name;
  protected int $birth_year;
  protected string $license_type;

  public function __construct($conn, string $name, int $birth_year, string $license_type) {
    $this->name = $name;
    $this->birth_year = $birth_year;
    $this->license_type = $license_type;

    $sql = "INSERT INTO driver (name, birth_year, license_type) VALUES ('$name', $birth_year, '$license_type')";

    if ($conn->query($sql) === TRUE) {
      echo "New driver created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  public static function update($conn, int $id, string $name, int $birth_year, string $license_type) {

    $sql = "UPDATE driver SET name = '$name', birth_year = $birth_year, license_type = '$license_type' WHERE id = $id";
  
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
        echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Birth year: " . $row["birth_year"]. "  - License type: " . $row["license_type"].  PHP_EOL;
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
    call_user_func($argv[1], isset($argv[2]) ? $argv[2] : null, isset($argv[3]) ? $argv[3] : null, isset($argv[4]) ? $argv[4] : null, isset($argv[5]) ? $argv[5] : null);
  }
}

/*function new_vehicle(string $licence_number, ?string $type = null, ?int $year_in = 0) {
  global $conn;
  $v = new Vehicle($conn, $licence_number, $type, $year_in);
}

function update_vehicle(string $licence_number, ?string $type = null, ?int $year_in = 0) {
  global $conn;
  Vehicle::update($conn, $licence_number, $type, $year_in);
}*/

function new_car(string $licence_number, string $type, int $year_in, int $number_of_persons_that_can_be_transported) {
  global $conn;
  $v = new Car($conn, $licence_number, $type, $year_in, $number_of_persons_that_can_be_transported);
}

function update_car(string $licence_number, string $type, int $year_in, int $number_of_persons_that_can_be_transported) {
  global $conn;
  Car::update_car($conn, $licence_number, $type, $number_of_persons_that_can_be_transported);
}

function new_pickup(string $licence_number, string $type, int $year_in, int $transportable_load) {
  global $conn;
  $v = new Pickup($conn, $licence_number, $type, $year_in, $transportable_load);
}

function update_pickup(string $licence_number, string $type, int $year_in, int $transportable_load) {
  global $conn;
  Pickup::update_pickup($conn, $licence_number, $type, $transportable_load);
}

function new_truck(string $licence_number, string $type, int $year_in, int $transportable_load) {
  global $conn;
  $v = new Truck($conn, $licence_number, $type, $year_in, $transportable_load);
}

function update_truck(string $licence_number, string $type, int $year_in, int $transportable_load) {
  global $conn;
  Truck::update_pickup($conn, $licence_number, $type, $transportable_load);
}

function delete_vehicle(string $licence_number) {
  global $conn;
  Vehicle::delete($conn, $licence_number);
}

function list_vehicles() {
  global $conn;
  Vehicle::list($conn);
}

function new_driver(string $name, int $birth_year, string $license_type) {
  global $conn;
  $d = new Driver($conn, $name, $birth_year, $license_type);
}

function update_driver(int $id, string $name, int $birth_year, string $license_type) {
  global $conn;
  Driver::update($conn, $id, $name, $birth_year, $license_type);
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

function list_vehicle_usage($from, $to) {
  global $conn;
  Vehicle::list_vehicle_usage($conn, $from, $to);
}

$conn->close();

?>