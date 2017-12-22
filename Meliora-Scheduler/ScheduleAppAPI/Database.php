<?php

CREATE DATABASE ScheduleApp;

USE ScheduleApp;
 
CREATE TABLE IF NOT EXISTS `schools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_name` varchar(500) NOT NULL,
  `secrete_question` varchar(455) NOT NULL,
  `secret_answer` text NOT NULL,   //Secret answer will be hashed
  `access_code` varchar(1000) NOT NULL,
  `API_link` text NOT NULL,  //Link to school's ScheduleAPP API
  `date_joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `Access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_code` varchar(1000) NOT NULL,
  `is_used` varchar(50) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
);


CREATE DATABASE UofR;

USE UofR;


CREATE TABLE IF NOT EXISTS `professors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prof_name` varchar(255) NOT NULL, 
  `prof_email` varchar(255) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `office_loc` varchar(1000) DEFAULT NULL,
  `department` varchar(300) NOT NULL,
  `api_key` text NOT NULL,
  `mesage_api_key` text NOT NULL,
  `office_hrs` json DEFAULT NULL,  
  `meeting_interval` int(11) DEFAULT NULL,	
  `full_schedule` json DEFAULT NULL, 
  `semester_start` varchar(50) DEFAULT NULL,  
  `semester_end` varchar(50) DEFAULT NULL, 
  `date_created` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prof_email` (`prof_email`)
);


CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_name` varchar(255) NOT NULL, 
  `student_email` varchar(255) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `api_key` text NOT NULL,
  `date_created` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_email` (`student_email`)
);



CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prof_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `appointment_time` varchar(50) NOT NULL, 
  `appointment_date` date NOT NULL,
  `reason_for_appointment` text NOT NULL,
  `is_completed` varchar(50) NOT NULL DEFAULT 'no',
  `is_cancelled` varchar(50) NOT NULL DEFAULT 'no',  
  `reason_cancel` text DEFAULT NULL, 
  `cancelled_by` text DEFAULT NULL, 
  PRIMARY KEY (`id`),
  KEY `prof_id` (`prof_id`),
  KEY `student_id` (`student_id`)
);

ALTER TABLE  `appointments` ADD FOREIGN KEY (  `prof_id` ) REFERENCES  `UofR`.`professors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE  `appointments` ADD FOREIGN KEY (  `student_id` ) REFERENCES  `UofR`.`students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

?>