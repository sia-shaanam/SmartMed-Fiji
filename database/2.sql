-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2024 at 10:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smf_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `contact_information` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `job_position` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `age` int(11) DEFAULT NULL CHECK (`age` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `name`, `specialization`, `contact_information`, `email`, `department`, `job_position`, `gender`, `age`) VALUES
('DOC001', 'YDS7L', 'shaanam prasad', '', '1516-546', 'dnman@niscnh', 'emergency', 'neurolgist', 'female', 22);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `login_id` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_date` date DEFAULT curdate(),
  `login_time` time DEFAULT curtime()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`login_id`, `email`, `password`, `login_date`, `login_time`) VALUES
('11225', 'shaanam.prasad@gmal.com', '$2y$10$.HcX.PrfsS9gwBPERdqaIuvfwEILuOsH1L4NdDUOnHs4/4bJ4smZG', '2024-10-01', '02:08:57'),
('shaanam', 'shinam2005@gmail.com', '$2y$10$/Hy.3tUQgLEM4p0DzWL4AOYJAJZCuoi2FL2qgaU55rcgxbyQCqlVi', '2024-10-03', '17:21:11'),
('sia45', 'saaa@gmail.com', '$2y$10$IY5Whha0SZI2V1fi/twJJ.ENcS4lpzQ0gxmFmBRnsrswuxXYsLTt.', '2024-10-01', '02:10:07'),
('testuser', 'jessica@gmail.com', '$2y$10$WTkKVOizCAzY3k2NIttSb.gIL62tKhdVARpZ3v/HXYtqkpl.RqKRq', '2024-10-01', '01:38:16'),
('YDS7L', 'shinam20ddva05@gmail.com', '$2y$10$XhzRqiZhoDAXzyjGS8hId.NdX0kkCCzuqstEJuMfFt8xSvPgi99XS', '2024-10-01', '01:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL,
  `patient_id` varchar(255) NOT NULL,
  `visit_date` date NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment_plans` text DEFAULT NULL,
  `test_results` text DEFAULT NULL,
  `blood_pressure` varchar(50) DEFAULT NULL,
  `heart_rate` varchar(50) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `primary_physician` varchar(255) DEFAULT NULL,
  `primary_physician_address` varchar(255) DEFAULT NULL,
  `primary_physician_contact` varchar(50) DEFAULT NULL,
  `secondary_physician` varchar(255) DEFAULT NULL,
  `secondary_physician_contact` varchar(50) DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `visit_reason` text DEFAULT NULL,
  `pregnancy_status` enum('Yes','No','N/A') DEFAULT 'N/A',
  `pregnancy_duration` varchar(50) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `previous_injuries` text DEFAULT NULL,
  `immunization_history` text DEFAULT NULL,
  `family_medical_history` text DEFAULT NULL,
  `insurance_carrier` varchar(255) DEFAULT NULL,
  `insurance_plan` varchar(255) DEFAULT NULL,
  `insurance_contact` varchar(50) DEFAULT NULL,
  `policy_number` varchar(255) DEFAULT NULL,
  `group_number` varchar(255) DEFAULT NULL,
  `ssn` varchar(50) DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `company_city` varchar(255) DEFAULT NULL,
  `company_state` varchar(50) DEFAULT NULL,
  `company_zip` varchar(50) DEFAULT NULL,
  `present_symptoms` text DEFAULT NULL,
  `symptom_details` text DEFAULT NULL,
  `symptom_duration` varchar(50) DEFAULT NULL,
  `symptom_severity` varchar(50) DEFAULT NULL,
  `exercise_frequency` varchar(255) DEFAULT NULL,
  `diet` varchar(255) DEFAULT NULL,
  `sleep_patterns` varchar(255) DEFAULT NULL,
  `stress_management` varchar(255) DEFAULT NULL,
  `substance_use` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`record_id`, `patient_id`, `visit_date`, `diagnosis`, `treatment_plans`, `test_results`, `blood_pressure`, `heart_rate`, `temperature`, `weight`, `height`, `primary_physician`, `primary_physician_address`, `primary_physician_contact`, `secondary_physician`, `secondary_physician_contact`, `medical_conditions`, `medications`, `visit_reason`, `pregnancy_status`, `pregnancy_duration`, `allergies`, `previous_injuries`, `immunization_history`, `family_medical_history`, `insurance_carrier`, `insurance_plan`, `insurance_contact`, `policy_number`, `group_number`, `ssn`, `employment_status`, `occupation`, `industry`, `company_name`, `company_address`, `company_city`, `company_state`, `company_zip`, `present_symptoms`, `symptom_details`, `symptom_duration`, `symptom_severity`, `exercise_frequency`, `diet`, `sleep_patterns`, `stress_management`, `substance_use`) VALUES
(1, 'P12345', '2024-10-04', 'Hypertension', 'wd', '55', '445/180', '55', 55.20, 12.32, 0.00, 'vmsldv', 'fsnvj', 'vjbs', 'vdjnl', 'nck', '', '', '', '', '', '', '', '', '', 'dbbskf', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `medical_record_doctors`
--

CREATE TABLE `medical_record_doctors` (
  `record_id` int(11) NOT NULL,
  `doctor_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_id` varchar(255) NOT NULL,
  `login_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `contact_information` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `job_position` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `age` int(11) DEFAULT NULL CHECK (`age` >= 0),
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `preferredName` varchar(255) DEFAULT NULL,
  `dob` date NOT NULL,
  `patientIdentifier` varchar(255) NOT NULL,
  `preferredPronouns` varchar(50) DEFAULT NULL,
  `maritalStatus` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `contactPreference` varchar(255) DEFAULT NULL,
  `emergencyContactName` varchar(255) NOT NULL,
  `relationship` varchar(255) NOT NULL,
  `emergencyContactNumber` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `login_id`, `name`, `specialization`, `contact_information`, `email`, `department`, `job_position`, `gender`, `age`, `firstName`, `lastName`, `preferredName`, `dob`, `patientIdentifier`, `preferredPronouns`, `maritalStatus`, `address`, `phone`, `contactPreference`, `emergencyContactName`, `relationship`, `emergencyContactNumber`) VALUES
('P12345', 'YDS7L', 'sia', 'Cardiology', '123 Main St, City', 'johndoe@example.com', 'Health Department', 'Doctor', 'male', 30, 'sia', '', 'Sia', '2024-11-02', '', 'She', 'single', 'Olosara', '8664148', 'moblie', 'lerra', 'mother', '8453');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('doctor','nurse','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `password`, `role`) VALUES
('sia', '$2y$10$NF8dMNkjZKs8pSk0L5XtUOaL7HE0ZE68X7P7tV8zvIAd0pxYbh2IS', 'doctor'),
('siama', '$2y$10$0fr0RgaXLJt1jt9qWvB2oe3RW9a56/zgBcSc0wL/yV7YiWT5AJPFa', 'admin'),
('YDS7L', '$2y$10$5pNZA781PH6cDMelK7WX6uAh7leNF9qws1G/YnWU3G7ZEMrt3aS9K', 'doctor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`login_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medical_record_doctors`
--
ALTER TABLE `medical_record_doctors`
  ADD PRIMARY KEY (`record_id`,`doctor_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `login_id` (`login_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`);

--
-- Constraints for table `medical_record_doctors`
--
ALTER TABLE `medical_record_doctors`
  ADD CONSTRAINT `medical_record_doctors_ibfk_1` FOREIGN KEY (`record_id`) REFERENCES `medical_records` (`record_id`),
  ADD CONSTRAINT `medical_record_doctors_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `patient_ibfk_1` FOREIGN KEY (`login_id`) REFERENCES `login` (`login_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
