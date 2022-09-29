CREATE TABLE car (
    licence_number VARCHAR(20) PRIMARY KEY,
    number_of_persons_that_can_be_transported INTEGER NOT NULL,
    drive_with VARCHAR(10) NOT NULL DEFAULT 'BCD', /* which driver licence is allowed */
    FOREIGN KEY (licence_number) REFERENCES vehicle(licence_number) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE pickup (
    licence_number VARCHAR(20) PRIMARY KEY,
    transportable_load INTEGER NOT NULL,
    drive_with VARCHAR(10) NOT NULL DEFAULT 'CD',
    FOREIGN KEY (licence_number) REFERENCES vehicle(licence_number) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE truck (
    licence_number VARCHAR(20) PRIMARY KEY,
    transportable_load INTEGER NOT NULL,
    drive_with VARCHAR(10) NOT NULL DEFAULT 'D',
    FOREIGN KEY (licence_number) REFERENCES vehicle(licence_number) ON UPDATE CASCADE ON DELETE CASCADE
);

ALTER TABLE driver
ADD license_type VARCHAR(1); /* only 1 is supported */