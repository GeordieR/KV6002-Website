-- Create Database
CREATE DATABASE KV6003;

-- Create User Table
CREATE TABLE KV6003.User (
	UserID INT AUTO_INCREMENT NOT NULL,
	UserEmail VARCHAR(50),
	UserPassword CHAR(40),
	IMAPServer VARCHAR(50),
	SMTPServer VARCHAR(50),
	IMAPPort SMALLINT,
	IMAPSSL Bool,
	SMTPPort SMALLINT,
	SMTPSSL Bool,
	SMTPAuth Bool,
	EmailUsername VARCHAR(50),
	EmailPassword CHAR(40),
	PRIMARY KEY(UserID)
);

-- Create Study Table
CREATE TABLE KV6003.Study (
	StudyID INT AUTO_INCREMENT NOT NULL,
	AttachmentPath CHAR(100) NOT NULL,
	RecipientEmail VARCHAR(50),
	UserID INT,
	Active Bool,
	ScheduleSent Bool DEFAULT 0,
	ScheduleTime DateTime,
	PRIMARY KEY(StudyID),
	FOREIGN KEY(UserID) REFERENCES User(UserID)
);

-- Create Example User
INSERT INTO KV6003.User (UserEmail, UserPassword, IMAPServer, SMTPServer, IMAPPort, IMAPSSL, SMTPPort, SMTPSSL, SMTPAuth, EmailUsername, EmailPassword)
VALUES ('kv6003@outlook.com', '[Hashed Password]', 'outlook.office365.com', 'smtp-mail.outlook.com', 993, 1, 587, 1, 1, 'kv6003@outlook.com', '[Un Hashed Outlook Password]');