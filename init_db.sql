CREATE DATABASE IF NOT EXISTS invoice_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE invoice_app;

CREATE TABLE companies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  logo VARCHAR(255),
  address TEXT,
  phone VARCHAR(64),
  bank_info TEXT,
  code_prefix CHAR(2) NOT NULL, -- e.g. OV
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  address TEXT,
  phone VARCHAR(64),
  email VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_id INT NOT NULL,
  client_id INT NOT NULL,
  invoice_number VARCHAR(64) NOT NULL UNIQUE,
  issue_date DATE NOT NULL,
  due_date DATE NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  tax_total DECIMAL(12,2) NOT NULL,
  total DECIMAL(12,2) NOT NULL,
  status VARCHAR(20) DEFAULT 'unpaid',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

CREATE TABLE invoice_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  description TEXT,
  qty DECIMAL(12,2),
  unit_price DECIMAL(12,2),
  tax_rate DECIMAL(5,2),
  line_total DECIMAL(12,2),
  FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);
