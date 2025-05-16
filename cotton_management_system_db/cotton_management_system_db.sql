-- Create the Database
CREATE DATABASE IF NOT EXISTS cotton_management_system;
USE cotton_management_system;

-- Farmer Table
CREATE TABLE farmer (
    farmer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    aadhaar_no VARCHAR(12) UNIQUE NOT NULL,
    location VARCHAR(100),
    phone_number VARCHAR(15),
    email VARCHAR(100),
    registration_date DATE
);

-- Farmer Address (Multivalued Attribute)
CREATE TABLE farmer_address (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT,
    address_line VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    FOREIGN KEY (farmer_id) REFERENCES farmer(farmer_id) ON DELETE CASCADE
);

-- Cotton Table
CREATE TABLE cotton (
    cotton_id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT,
    agent_id INT,
    type VARCHAR(50),
    trash DECIMAL(5,2),
    moisture DECIMAL(5,2),
    description TEXT,
    quantity DECIMAL(10,2),
    price_per_kg DECIMAL(10,2), -- Price per kg of cotton
    produce_date DATE,
    status ENUM('Available', 'Sold', 'Reserved') DEFAULT 'Available',
    image_url VARCHAR(255),
    FOREIGN KEY (farmer_id) REFERENCES farmer(farmer_id),
    FOREIGN KEY (agent_id) REFERENCES agent(agent_id)
);

-- Purchaser Table
CREATE TABLE purchaser (
    purchaser_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    contact_number VARCHAR(15),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Store hashed password
    registered_date DATE
);
ALTER TABLE purchaser ADD COLUMN otp VARCHAR(10) DEFAULT NULL;

-- Agent Table
CREATE TABLE agent (
    agent_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(15),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Store hashed password
    address VARCHAR(255)
);

-- Account Table (shared for Farmer and Purchaser)
CREATE TABLE account (
    account_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('Farmer', 'Purchaser') NOT NULL,
    bank_name VARCHAR(100),
    branch_name VARCHAR(100),
    account_type ENUM('Savings', 'Current'),
    ifsc_code VARCHAR(15),
    account_number VARCHAR(20)
    -- Note: You can validate user existence in your app logic using user_type
);

-- Purchase Table
CREATE TABLE purchase (
    purchase_id INT AUTO_INCREMENT PRIMARY KEY,
    cotton_id INT,
    purchaser_id INT,
    agent_id INT,
    lot_number VARCHAR(50),
    purchase_date DATE,
    quantity DECIMAL(10,2),
    price DECIMAL(10,2), -- Price per kg at purchase time (may differ from cotton.price_per_kg)
    total_amount DECIMAL(12,2), -- Calculated as quantity * price
    payment_status ENUM('Pending', 'Completed') DEFAULT 'Pending',
    FOREIGN KEY (cotton_id) REFERENCES cotton(cotton_id),
    FOREIGN KEY (purchaser_id) REFERENCES purchaser(purchaser_id),
    FOREIGN KEY (agent_id) REFERENCES agent(agent_id)
);
ALTER TABLE purchase 
ADD COLUMN agent_commission DECIMAL(10,2) DEFAULT 0;


-- Payment Table
CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    purchaser_id INT,
    agent_id INT,
    amount DECIMAL(10,2),
    transaction_id VARCHAR(100),
    payment_mode ENUM('Cash', 'Bank Transfer', 'UPI'),
    status ENUM('Pending', 'Success', 'Failed') DEFAULT 'Pending',
    payment_date DATE,
    FOREIGN KEY (purchaser_id) REFERENCES purchaser(purchaser_id),
    FOREIGN KEY (agent_id) REFERENCES agent(agent_id)
);

ALTER TABLE purchase 
MODIFY payment_status VARCHAR(20) NOT NULL DEFAULT 'Pending';


-- Insert a Farmer
INSERT INTO farmer (name, aadhaar_no, location, phone_number, email)
VALUES ('John Doe', '123456789012', 'Village A', '9876543210', 'john.doe@example.com');

-- Insert Farmer Address
INSERT INTO farmer_address (farmer_id, address_line, city, state, pincode)
VALUES (1, '123 Farm Lane', 'Village A', 'StateX', '123456');

-- Insert an Agent (password example assumes hashed string)
INSERT INTO agent (name, contact, email, password, address)
VALUES ('chetan', '9876543210', 'admin@gmail.com', '1234', 'Agent Address 1');
-- new agent
INSERT INTO agent (name, contact, email, password, address)
VALUES ('veeresh', '9876543210', 'veeresh@gmail.com', 'Veeresh@123', 'Agent Address 1');
-- Insert a Purchaser (password example assumes hashed string)
INSERT INTO purchaser (name, location, contact_number, email, password)
VALUES ('Basavaraj', 'CityY', '9123456789', 'basu@gmail.com', '1234');

-- Insert Cotton uploaded by Agent for Farmer
INSERT INTO cotton (farmer_id, agent_id, type, trash, moisture, description, quantity, price_per_kg, produce_date, status, image_url)
VALUES (1, 1, 'Organic Cotton', 1.5, 8.2, 'High quality organic cotton', 100.5, 150.00, '2024-04-01', 'Available', 'http://example.com/image1.jpg');

-- Insert a Purchase for that cotton by Purchaser through Agent
INSERT INTO purchase (cotton_id, purchaser_id, agent_id, lot_number, purchase_date, quantity, price, total_amount, payment_status)
VALUES (1, 1, 1, 'LOT1001', '2024-04-15', 50.0, 155.00, 7750.00, 'Pending');

-- Fetch cotton details along with farmer and agent info
SELECT c.cotton_id, c.type, c.quantity, c.price_per_kg, f.name AS farmer_name, a.name AS agent_name
FROM cotton c
JOIN farmer f ON c.farmer_id = f.farmer_id
JOIN agent a ON c.agent_id = a.agent_id;

-- Fetch purchase details with purchaser, cotton and payment status
SELECT p.purchase_id, pur.name AS purchaser_name, c.type AS cotton_type, p.quantity, p.price, p.total_amount, p.payment_status
FROM purchase p
JOIN purchaser pur ON p.purchaser_id = pur.purchaser_id
JOIN cotton c ON p.cotton_id = c.cotton_id;

-- Fetch agent login info (email and hashed password)
SELECT email, password FROM agent WHERE email = 'agent.smith@example.com';

-- Fetch purchaser login info (email and hashed password)
SELECT email, password FROM purchaser WHERE email = 'alice.buyer@example.com';

select * from agent;

select * from purchaser;

select * from cotton;

desc cotton;