CREATE TABLE driver (
    id int(6) AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    birth_year int(4) NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE vehicle_usage (
    driver_id int NOT NULL,
    vehicle_licence_number VARCHAR(20) NOT NULL,
    usage_date DATE DEFAULT CURRENT_DATE,
    PRIMARY KEY (vehicle_licence_number, usage_date),
    FOREIGN KEY (driver_id) REFERENCES driver(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (vehicle_licence_number) REFERENCES vehicle(licence_number) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE UNIQUE INDEX vehicle_usage_driver_id_usage_date_idx ON vehicle_usage (driver_id, usage_date);