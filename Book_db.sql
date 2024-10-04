CREATE TABLE books (
    BookID INT AUTO_INCREMENT PRIMARY KEY,
    BookName VARCHAR(255) NOT NULL,
    BookDescription TEXT NOT NULL,
    QuantityAvailable INT NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    Author VARCHAR(255) NOT NULL,
    PublishedYear YEAR NOT NULL,
    
);
