<?php
namespace App;

class Database
{
    private $connection;

    public function __construct()
    {
        $host = '127.0.0.1';
        $dbname = 'campusjobs';
        $username = 'campusjobs';
        $password = 'JjseKOHzkmrSwBzy';
        $port = 3036;

        $this->connection = new \PDO(
            "mysql:host=$host;port=$port;dbname=$dbname",
            $username,
            $password
        );
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


        // Create contacts table if it doesn't exist
        $this->connection->exec("
            -- Create Students table
CREATE TABLE IF NOT EXISTS Students (
    student_id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    PRIMARY KEY (student_id)
) ENGINE=InnoDB;

-- Create Recruiters table
CREATE TABLE IF NOT EXISTS Recruiters (
    recruiter_id INT NOT NULL AUTO_INCREMENT,
    student_id INT NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    recruiter_created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    recruiter_updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (recruiter_id),
    FOREIGN KEY (student_id) REFERENCES Students(student_id)
) ENGINE=InnoDB;

-- Insert sample students
INSERT INTO Students (first_name, last_name, email, password) VALUES
('John', 'Doe', 'john.doe@example.com', 'secure123'),
('Jane', 'Smith', 'jane.smith@example.com', 'secure456'),
('Robert', 'Johnson', 'robert.j@example.com', 'secure789');

-- Insert sample recruiters
INSERT INTO Recruiters (student_id, company_name) VALUES
(1, 'Student Ambassadors'),
(2, 'Student Assistant'),
(3, 'Research Assistant');
        ");
    }

    public function getContacts()
    {
        $stmt = $this->connection->query("SELECT * FROM contacts");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}