const sqlite3 = require('sqlite3').verbose();
const express = require('express');
const app = express();
const PORT = 3000;

// Open a SQLite database (it will create the file if it doesn't exist)
const db = new sqlite3.Database('./campus_jobs.db', (err) => {
  if (err) {
    console.error('Error opening the database:', err.message);
    return;
  }
  console.log('Connected to SQLite database!');
});

// Create tables if they don't exist
const createTablesQuery = `
  CREATE TABLE IF NOT EXISTS Employers (
    employer_id INTEGER PRIMARY KEY AUTOINCREMENT,
    company_name TEXT NOT NULL,
    contact_person TEXT NOT NULL,
    contact_email TEXT NOT NULL UNIQUE,
    contact_phone TEXT
  );

  CREATE TABLE IF NOT EXISTS Jobs (
    job_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employer_id INTEGER NOT NULL,
    job_title TEXT NOT NULL,
    job_description TEXT NOT NULL,
    salary REAL,
    location TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES Employers (employer_id) ON DELETE CASCADE
  );

  CREATE TABLE IF NOT EXISTS Students (
    student_id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('student', 'admin', 'employer'))
  );
`;

db.serialize(() => {
  db.run(createTablesQuery, (err) => {
    if (err) {
      console.error('Error creating tables:', err);
    } else {
      console.log('Tables created or already exist.');
    }
  });
});

// Middleware to parse JSON bodies
app.use(express.json());

// API Routes
app.get('/api/users', (req, res) => {
  db.all('SELECT * FROM Students', [], (err, rows) => {
    if (err) {
      res.status(400).json({ error: err.message });
      return;
    }
    res.json(rows);
  });
});

app.post('/api/users', (req, res) => {
  const { first_name, last_name, email, password, role } = req.body;

  const query = 'INSERT INTO Students (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)';
  db.run(query, [first_name, last_name, email, password, role], function (err) {
    if (err) {
      res.status(400).json({ error: err.message });
      return;
    }
    res.json({ id: this.lastID });
  });
});

// Start the server
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
