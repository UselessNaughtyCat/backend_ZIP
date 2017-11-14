-- CREATE DATABASE ufanet_work;

CREATE TABLE `PC` (
	`id` int NOT NULL AUTO_INCREMENT,
	`HDD` varchar(250) NOT NULL,
	`RAM` varchar(250) NOT NULL,
	`processor` varchar(250) NOT NULL,
	`domain_name` varchar(250) NOT NULL,
	`comment` TEXT,
	`MAC` varchar(17) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Monitor` (
	`id` int NOT NULL AUTO_INCREMENT,
	`diagonal` int NOT NULL,
	`name` varchar(250) NOT NULL,
	`video_output` varchar(250) NOT NULL,
	`comment` TEXT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Headphone` (
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(250) NOT NULL,
	`comment` TEXT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Video_Output` (
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(250) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `PC_Video_Outputs` (
	`id` int NOT NULL AUTO_INCREMENT,
	`PC_id` int NOT NULL,
	`Video_Output_id` int NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Monitor_Video_Outputs` (
	`id` int NOT NULL AUTO_INCREMENT,
	`Monitor_id` int NOT NULL,
	`Video_Output_id` int NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Workspace` (
	`id` int NOT NULL AUTO_INCREMENT,
	`comment` TEXT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Workspace_Monitors` (
	`id` int NOT NULL AUTO_INCREMENT,
	`Workspace_id` int NOT NULL,
	`Monitor_id` int NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Workspace_PCs` (
	`id` int NOT NULL AUTO_INCREMENT,
	`Workspace_id` int NOT NULL,
	`PC_id` int NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `Workspace_Headphones` (
	`id` int NOT NULL AUTO_INCREMENT,
	`Workspace_id` int NOT NULL,
	`Headphone_id` int NOT NULL,
	PRIMARY KEY (`id`)
);

ALTER TABLE `PC_Video_Outputs` ADD CONSTRAINT `PC_Video_Outputs_fk0` FOREIGN KEY (`PC_id`) REFERENCES `PC`(`id`) ON UPDATE CASCADE;

ALTER TABLE `PC_Video_Outputs` ADD CONSTRAINT `PC_Video_Outputs_fk1` FOREIGN KEY (`Video_Output_id`) REFERENCES `Video_Output`(`id`) ON UPDATE CASCADE;

ALTER TABLE `Monitor_Video_Outputs` ADD CONSTRAINT `Monitor_Video_Outputs_fk0` FOREIGN KEY (`Monitor_id`) REFERENCES `Monitor`(`id`) ON UPDATE CASCADE;

ALTER TABLE `Monitor_Video_Outputs` ADD CONSTRAINT `Monitor_Video_Outputs_fk1` FOREIGN KEY (`Video_Output_id`) REFERENCES `Video_Output`(`id`) ON UPDATE CASCADE;

ALTER TABLE `Workspace_Monitors` ADD CONSTRAINT `Workspace_Monitors_fk0` FOREIGN KEY (`Workspace_id`) REFERENCES `Workspace`(`id`) ON UPDATE CASCADE;

ALTER TABLE `Workspace_Monitors` ADD CONSTRAINT `Workspace_Monitors_fk1` FOREIGN KEY (`Monitor_id`) REFERENCES `Monitor`(`id`) ON UPDATE CASCADE;

ALTER TABLE `Workspace_PCs` ADD CONSTRAINT `Workspace_PCs_fk0` FOREIGN KEY (`Workspace_id`) REFERENCES `Workspace`(`id`) ON UPDATE CASCADE;