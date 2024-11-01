-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2024 at 02:33 PM
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
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `adm_Id` int(10) NOT NULL,
  `adm_firstName` varchar(50) NOT NULL,
  `adm_lastName` varchar(50) NOT NULL,
  `adm_emailAddress` varchar(50) NOT NULL,
  `adm_password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`adm_Id`, `adm_firstName`, `adm_lastName`, `adm_emailAddress`, `adm_password`) VALUES
(1, 'Admin', '', 'admin@mail.com', '827ccb0eea8a706c4c34a16891f84e7b');

-- --------------------------------------------------------

--
-- Table structure for table `tblattendance`
--

CREATE TABLE `tblattendance` (
  `Att_id` int(11) NOT NULL,
  `Att_date` date NOT NULL,
  `std_id` int(11) NOT NULL,
  `att_hr_1` int(11) NOT NULL DEFAULT 1,
  `att_hr_2` int(11) NOT NULL DEFAULT 1,
  `att_hr_3` int(11) NOT NULL DEFAULT 1,
  `att_hr_4` int(11) NOT NULL DEFAULT 1,
  `att_hr_5` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblattendance`
--

INSERT INTO `tblattendance` (`Att_id`, `Att_date`, `std_id`, `att_hr_1`, `att_hr_2`, `att_hr_3`, `att_hr_4`, `att_hr_5`) VALUES
(2, '2024-10-19', 1, 1, 1, 1, 1, 1),
(3, '2024-10-20', 1, 1, 1, 1, 1, 1),
(4, '2024-10-21', 1, 1, 1, 1, 1, 1),
(5, '2024-10-22', 1, 1, 1, 1, 1, 1),
(6, '2024-10-23', 1, 1, 1, 1, 1, 1),
(7, '2024-10-24', 1, 1, 1, 1, 1, 1),
(8, '2024-10-25', 1, 1, 1, 1, 1, 1),
(9, '2024-10-26', 1, 1, 1, 1, 1, 1),
(10, '2024-10-27', 1, 1, 1, 1, 1, 1),
(11, '2024-10-28', 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblcourse`
--

CREATE TABLE `tblcourse` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(30) NOT NULL,
  `course_start` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcourse`
--

INSERT INTO `tblcourse` (`course_id`, `course_name`, `course_start`) VALUES
(1, 'BCA', '2024'),
(2, 'BBA', '2024');

-- --------------------------------------------------------

--
-- Table structure for table `tblcourseincharge`
--

CREATE TABLE `tblcourseincharge` (
  `course_id` int(11) NOT NULL,
  `tea_id` int(11) NOT NULL,
  `isActive` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcourseincharge`
--

INSERT INTO `tblcourseincharge` (`course_id`, `tea_id`, `isActive`) VALUES
(1, 1, 1),
(2, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblcoursetaken`
--

CREATE TABLE `tblcoursetaken` (
  `course_taken_id` int(11) NOT NULL,
  `std_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `isActive` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcoursetaken`
--

INSERT INTO `tblcoursetaken` (`course_taken_id`, `std_id`, `course_id`, `isActive`) VALUES
(1, 1, 2, 0),
(2, 1, 1, 1),
(3, 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblexam`
--

CREATE TABLE `tblexam` (
  `exam_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `exam_date` date NOT NULL,
  `maximum_marks` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblexam_stu`
--

CREATE TABLE `tblexam_stu` (
  `tblexam_stu_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `std_id` int(11) NOT NULL,
  `marks_obtained` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblnotification`
--

CREATE TABLE `tblnotification` (
  `notification_id` int(11) NOT NULL,
  `notification_text` text NOT NULL,
  `notification_status` int(11) NOT NULL,
  `notification_title` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblnotification`
--

INSERT INTO `tblnotification` (`notification_id`, `notification_text`, `notification_status`, `notification_title`) VALUES
(1, 'We are excited to welcome you to the new academic year! Make sure to check your schedules and course materials.', 1, 'Welcome to the New Academic '),
(2, 'The schedule for the upcoming midterm exams has been released. Please check the student portal for details.', 1, 'Midterm Exam Schedule'),
(3, 'Please remember that attendance is mandatory for all classes. Make sure to log in during your scheduled sessions.', 1, 'Attendance Policy Reminder'),
(4, 'Join us for a workshop on career development this Friday at 2 PM in the main auditorium. All students are encouraged to attend.', 1, 'Workshop on Career Development'),
(5, 'Classes will be suspended for the holiday break from December 20th to January 2nd. Enjoy your holidays!', 1, 'Holiday Break Announcement'),
(8, 'sample Text ,sample text', 1, 'Sample Title'),
(9, 'sample Text ,sample text 1', 1, 'Sample Title');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `std_id` int(10) NOT NULL,
  `std_firstName` varchar(255) NOT NULL,
  `std_lastName` varchar(255) NOT NULL,
  `std_otherName` varchar(255) DEFAULT NULL,
  `std_admissionNumber` varchar(255) NOT NULL,
  `std_password` varchar(50) NOT NULL,
  `std_dateCreated` varchar(50) NOT NULL,
  `std_email` varchar(255) NOT NULL DEFAULT 'not_provided@example.com',
  `std_phone_number` varchar(20) NOT NULL DEFAULT '0000000000',
  `stud_dob` date DEFAULT NULL,
  `std_address` text NOT NULL,
  `std_aadhar_no` int(14) NOT NULL,
  `std_parent_name` varchar(30) NOT NULL,
  `std_parent_ph` int(12) NOT NULL,
  `std_status` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`std_id`, `std_firstName`, `std_lastName`, `std_otherName`, `std_admissionNumber`, `std_password`, `std_dateCreated`, `std_email`, `std_phone_number`, `stud_dob`, `std_address`, `std_aadhar_no`, `std_parent_name`, `std_parent_ph`, `std_status`) VALUES
(1, 'Alice', 'Smith', NULL, 'ADM12345', '827ccb0eea8a706c4c34a16891f84e7b', '2024-10-27', 'vishalvnair124@gmail.com', '09526212285', NULL, 'Saraswathy vilasam mancode po ,kalanjoor', 2147483647, '', 2147483647, 1),
(2, 'VISHAL', 'NAIR', '', 'ADM2', '827ccb0eea8a706c4c34a16891f84e7b', '2024-10-31 15:36:40', 'vishalvnair0124@gmail.com', '9539109602', '2003-10-25', 'Saraswathy vilasam mancode po ,kalanjoor', 2147483647, 'B VIKRAMAN', 2147483647, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblteachers`
--

CREATE TABLE `tblteachers` (
  `tea_id` int(10) NOT NULL,
  `tea_firstName` varchar(255) NOT NULL,
  `tea_lastName` varchar(255) NOT NULL,
  `tea_emailAddress` varchar(255) NOT NULL,
  `tea_password` varchar(255) NOT NULL,
  `tea_phoneNo` varchar(50) NOT NULL,
  `tea_dateCreated` varchar(50) NOT NULL,
  `tea_address` text NOT NULL,
  `tea_is_assigned` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblteachers`
--

INSERT INTO `tblteachers` (`tea_id`, `tea_firstName`, `tea_lastName`, `tea_emailAddress`, `tea_password`, `tea_phoneNo`, `tea_dateCreated`, `tea_address`, `tea_is_assigned`) VALUES
(1, 'John', 'Doe', 'teacher@example.com', '827ccb0eea8a706c4c34a16891f84e7b', '9876543210', '2024-10-27', '123 Main St', 1),
(3, 'Will', 'Kibagendi', 'teacher2@mail.com', '827ccb0eea8a706c4c34a16891f84e7b', '09089898999', '2022-10-31', 'Nairobi, Kenya', 1),
(15, 'VISHAL', 'NAIR', 'vishalvnair124@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '09526212285', '2024-10-30 16:17:03', 'Saraswathy vilasam mancode po ,kalanjoor', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`adm_Id`);

--
-- Indexes for table `tblattendance`
--
ALTER TABLE `tblattendance`
  ADD PRIMARY KEY (`Att_id`),
  ADD KEY `FK_std_id` (`std_id`);

--
-- Indexes for table `tblcourse`
--
ALTER TABLE `tblcourse`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `tblcourseincharge`
--
ALTER TABLE `tblcourseincharge`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `tblcoursetaken`
--
ALTER TABLE `tblcoursetaken`
  ADD PRIMARY KEY (`course_taken_id`),
  ADD KEY `std_id` (`std_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `tblexam`
--
ALTER TABLE `tblexam`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `tblexam_stu`
--
ALTER TABLE `tblexam_stu`
  ADD PRIMARY KEY (`tblexam_stu_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `std_id` (`std_id`);

--
-- Indexes for table `tblnotification`
--
ALTER TABLE `tblnotification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`std_id`),
  ADD UNIQUE KEY `std_admissionNumber` (`std_admissionNumber`);

--
-- Indexes for table `tblteachers`
--
ALTER TABLE `tblteachers`
  ADD PRIMARY KEY (`tea_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `adm_Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblattendance`
--
ALTER TABLE `tblattendance`
  MODIFY `Att_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tblcourse`
--
ALTER TABLE `tblcourse`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblcourseincharge`
--
ALTER TABLE `tblcourseincharge`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblcoursetaken`
--
ALTER TABLE `tblcoursetaken`
  MODIFY `course_taken_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblexam`
--
ALTER TABLE `tblexam`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblexam_stu`
--
ALTER TABLE `tblexam_stu`
  MODIFY `tblexam_stu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblnotification`
--
ALTER TABLE `tblnotification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `std_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblteachers`
--
ALTER TABLE `tblteachers`
  MODIFY `tea_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblattendance`
--
ALTER TABLE `tblattendance`
  ADD CONSTRAINT `FK_std_id` FOREIGN KEY (`std_id`) REFERENCES `tblstudents` (`std_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblcoursetaken`
--
ALTER TABLE `tblcoursetaken`
  ADD CONSTRAINT `tblcoursetaken_ibfk_1` FOREIGN KEY (`std_id`) REFERENCES `tblstudents` (`std_id`),
  ADD CONSTRAINT `tblcoursetaken_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `tblcourse` (`course_id`);

--
-- Constraints for table `tblexam`
--
ALTER TABLE `tblexam`
  ADD CONSTRAINT `tblexam_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `tblcourse` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblexam_stu`
--
ALTER TABLE `tblexam_stu`
  ADD CONSTRAINT `tblexam_stu_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `tblexam` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblexam_stu_ibfk_2` FOREIGN KEY (`std_id`) REFERENCES `tblstudents` (`std_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
