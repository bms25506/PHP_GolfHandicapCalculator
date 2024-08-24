-- Database creation
CREATE DATABASE IF NOT EXISTS `golf_handicap_calculator`;
USE `golf_handicap_calculator`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `first_name` varchar(50) NOT NULL,
    `last_name` varchar(50) NOT NULL,
    `age` int(11) DEFAULT NULL,
    `gender` enum('Male', 'Female', 'Other') DEFAULT NULL,
    PRIMARY KEY (`id`)
);

-- Scores table
CREATE TABLE IF NOT EXISTS `scores` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `course_id` int(11) NOT NULL,
    `score` int(11) NOT NULL,
    `holes` int(2) DEFAULT NULL,
    `date_played` datetime NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `handicap_index` float DEFAULT NULL,
    `handicap` float DEFAULT NULL,
    PRIMARY KEY (`id`)
);

-- Courses table
CREATE TABLE IF NOT EXISTS `courses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `course_name` varchar(255) NOT NULL,
    `city` varchar(255) DEFAULT NULL,
    `state` varchar(255) DEFAULT NULL,
    `tee_name` varchar(50) DEFAULT NULL,
    `gender` char(1) DEFAULT NULL,
    `par` int(11) DEFAULT NULL,
    `course_rating` decimal(4,1) DEFAULT NULL,
    `bogey_rating` decimal(4,1) DEFAULT NULL,
    `slope_rating` int(11) DEFAULT NULL,
    `front_nine_rating` varchar(50) DEFAULT NULL,
    `back_nine_rating` varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`)
);

-- Handicap table
CREATE TABLE IF NOT EXISTS `handicap` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `course_id` int(11) NOT NULL,
    `date_calculated` datetime NOT NULL,
    `handicap_index` decimal(5,2) NOT NULL,
    `handicap` decimal(5,2) NOT NULL,
    PRIMARY KEY (`id`)
);
