CREATE TABLE IF NOT EXISTS quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origin_amount DECIMAL(10, 2) NOT NULL,
    destination_amount DECIMAL(10, 2) NOT NULL,
    origin_currency VARCHAR(10) NOT NULL,
    destination_currency VARCHAR(10) NOT NULL,
    user_id INT NOT NULL,
    exchange_date DATETIME NOT NULL,
    exchange_rate DECIMAL(10, 6) NOT NULL,
    status VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
