CREATE TABLE mpesa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    amount DECIMAL(10,2),
    code VARCHAR(50),
    created_at DATE NOT NULL
);

CREATE TABLE paid_bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    amount DECIMAL(10,2),
    invoice_code VARCHAR(50),
    created_at DATE NOT NULL
);

CREATE TABLE unpaid_bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    amount DECIMAL(10,2),
    invoice_code VARCHAR(50),
    created_at DATE NOT NULL
);

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    namee VARCHAR(255),
    amount DECIMAL(10,2),
    created_at DATE NOT NULL
);

CREATE TABLE complimentary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10,2),
    invoice_code VARCHAR(50),
    created_at DATE NOT NULL
);

CREATE TABLE cancelled_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amount DECIMAL(10,2),
    invoice_code VARCHAR(50),
    created_at DATE NOT NULL
);
