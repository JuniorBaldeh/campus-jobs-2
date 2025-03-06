BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "Employers" (
	"employer_id"	INTEGER,
	"company_name"	TEXT NOT NULL,
	"contact_person"	TEXT NOT NULL,
	"contact_email"	TEXT NOT NULL UNIQUE,
	"contact_phone"	TEXT,
	PRIMARY KEY("employer_id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "Jobs" (
	"job_id"	INTEGER,
	"employer_id"	INTEGER NOT NULL,
	"job_title"	TEXT NOT NULL,
	"job_description"	TEXT NOT NULL,
	"salary"	REAL,
	"location"	TEXT,
	"created_at"	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY("job_id" AUTOINCREMENT),
	FOREIGN KEY("employer_id") REFERENCES "Employers"("employer_id") ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS "Applications" (
	"application_id"	INTEGER,
	"job_id"	INTEGER NOT NULL,
	"user_id"	INTEGER NOT NULL,
	"cover_letter"	TEXT,
	"resume"	TEXT,
	"applied_at"	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	"status"	TEXT DEFAULT 'pending' CHECK("status" IN ('pending', 'accepted', 'rejected')),
	PRIMARY KEY("application_id" AUTOINCREMENT),
	FOREIGN KEY("user_id") REFERENCES "Users"("user_id") ON DELETE CASCADE,
	FOREIGN KEY("job_id") REFERENCES "Jobs"("job_id") ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS "Reviews" (
	"review_id"	INTEGER,
	"employer_id"	INTEGER NOT NULL,
	"user_id"	INTEGER NOT NULL,
	"rating"	INTEGER CHECK("rating" BETWEEN 1 AND 5),
	"review_text"	TEXT,
	"created_at"	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY("review_id" AUTOINCREMENT),
	FOREIGN KEY("user_id") REFERENCES "Users"("user_id") ON DELETE CASCADE,
	FOREIGN KEY("employer_id") REFERENCES "Employers"("employer_id") ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS "Admins" (
	"admin_id"	INTEGER,
	"user_id"	INTEGER NOT NULL UNIQUE,
	"permissions"	TEXT DEFAULT 'full',
	PRIMARY KEY("admin_id" AUTOINCREMENT),
	FOREIGN KEY("user_id") REFERENCES "Users"("user_id") ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS "Students" (
	"student_id"	INTEGER,
	"first_name"	TEXT NOT NULL,
	"last_name"	TEXT NOT NULL,
	"email"	TEXT NOT NULL UNIQUE,
	"password"	TEXT NOT NULL,
	"role"	TEXT NOT NULL CHECK("role" IN ('student', 'admin', 'employer')),
	PRIMARY KEY("student_id" AUTOINCREMENT)
);
INSERT INTO "Students" VALUES (1,'John','Doe','john.doe@example.com','securepassword','student');
INSERT INTO "Students" VALUES (2,'Akeel','A','Akeel.A@example.com','securepassword2','student');
INSERT INTO "Students" VALUES (3,'Junior','Yaya','yaya.jr@example.com','securepassword3','student');
INSERT INTO "Students" VALUES (4,'Mukhatr','N','mukhtar.N@example.com','securepassword4','student');
COMMIT;
