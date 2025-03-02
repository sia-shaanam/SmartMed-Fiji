CREATE TABLE users (
    user_id VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    role ENUM('doctor', 'nurse', 'admin') NOT NULL
);



CREATE TABLE doctors (
    doctor_id VARCHAR(255) PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    specialization VARCHAR(255),
    contact_information VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    department VARCHAR(255),
    job_position VARCHAR(255),
    gender ENUM('male', 'female', 'other') NOT NULL,
    age INT CHECK (age >= 0),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

INSERT INTO doctors (doctor_id, user_id, name, specialization, contact_information, email, department, job_position, gender, age) 
VALUES ('DOC001', 'YDS7L', 'Dr. John Doe', 'Cardiology', '123-456-7890', 'johndoe@example.com', 'Cardiology', 'Consultant', 'male', 35);



CREATE TABLE Patient (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,               -- Unique identifier for the patient
    firstName VARCHAR(255) NOT NULL,                        -- Patient's first name
    lastName VARCHAR(255) NOT NULL,                         -- Patient's last name
    preferredName VARCHAR(255),                             -- Patient's preferred name
    dob DATE NOT NULL,                                      -- Date of birth
    patientIdentifier VARCHAR(255) NOT NULL UNIQUE,        -- Unique patient identifier
    gender VARCHAR(50) NOT NULL,                            -- Patient's gender
    preferredPronouns VARCHAR(50),                          -- Patient's preferred pronouns
    maritalStatus VARCHAR(50) NOT NULL,                    -- Marital status
    address VARCHAR(255) NOT NULL,                          -- Patient's address
    email VARCHAR(255),                                     -- Patient's email
    phone VARCHAR(50) NOT NULL,                             -- Patient's phone number
    contactPreference VARCHAR(255),                         -- Preferred method of contact
    emergencyContactName VARCHAR(255) NOT NULL,            -- Emergency contact's name
    relationship VARCHAR(255) NOT NULL,                    -- Relationship to emergency contact
    emergencyContactNumber VARCHAR(50) NOT NULL            -- Emergency contact's phone number
);
