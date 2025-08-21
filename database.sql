-- database.sql
CREATE DATABASE IF NOT EXISTS matrimonial;
USE matrimonial;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Expanded Biodata table
CREATE TABLE IF NOT EXISTS biodata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    dob DATE,
    age INT,
    gender VARCHAR(20),
    religion VARCHAR(50),
    caste VARCHAR(50),
    nationality VARCHAR(50),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    height DECIMAL(5,2),
    weight DECIMAL(6,2),
    blood_group VARCHAR(5),
    complexion VARCHAR(50),
    education VARCHAR(150),
    occupation VARCHAR(150),
    annual_income VARCHAR(50),
    work_location VARCHAR(100),
    father_name VARCHAR(150),
    father_occupation VARCHAR(150),
    mother_name VARCHAR(150),
    mother_occupation VARCHAR(150),
    siblings INT,
    preferred_age_min INT,
    preferred_age_max INT,
    preferred_education VARCHAR(150),
    preferred_location VARCHAR(100),
    hobbies TEXT,
    about_me TEXT,
    expectations TEXT,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
