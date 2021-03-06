-- MySQL Script generated by MySQL Workbench
-- qui 01 dez 2016 20:50:42 BRST
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema sys_permission
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `system_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `system_group` ;

CREATE TABLE IF NOT EXISTS `system_group` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_name` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `group_name_UNIQUE` (`group_name` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `system_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `system_user` ;

CREATE TABLE IF NOT EXISTS `system_user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(120) NOT NULL,
  `password` VARCHAR(60) NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `avatar` VARCHAR(255) NOT NULL DEFAULT 'default.png',
  `create_date` DATETIME NOT NULL,
  `update_date` DATETIME NULL,
  `delete_date` DATETIME NULL,
  `user_type` CHAR(1) NOT NULL DEFAULT 'S' COMMENT 'M = master\nG = gerente\nS = suporte\nF = usuario final',
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `can_edit` TINYINT(1) NOT NULL DEFAULT 1,
  `can_login` TINYINT(1) NOT NULL DEFAULT 0,
  `can_use_web` TINYINT(1) NOT NULL DEFAULT 0,
  `can_use_api` TINYINT(1) NOT NULL DEFAULT 0,
  `system_group_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `system_group_id`),
  UNIQUE INDEX `un_username` (`username` ASC),
  INDEX `fk_system_user_system_group1_idx` (`system_group_id` ASC),
  CONSTRAINT `fk_system_user_system_group1`
    FOREIGN KEY (`system_group_id`)
    REFERENCES `system_group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `system_resources`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `system_resources` ;

CREATE TABLE IF NOT EXISTS `system_resources` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT 1,
  `permission` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `user_resources`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_resources` ;

CREATE TABLE IF NOT EXISTS `user_resources` (
  `system_user_id` INT UNSIGNED NOT NULL,
  `system_resources_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`system_user_id`, `system_resources_id`),
  INDEX `fk_system_user_has_system_resources_system_resources1_idx` (`system_resources_id` ASC),
  INDEX `fk_system_user_has_system_resources_system_user1_idx` (`system_user_id` ASC),
  UNIQUE INDEX `un_user_resource` (`system_user_id` ASC, `system_resources_id` ASC),
  CONSTRAINT `fk_system_user_has_system_resources_system_user1`
    FOREIGN KEY (`system_user_id`)
    REFERENCES `system_user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_user_has_system_resources_system_resources1`
    FOREIGN KEY (`system_resources_id`)
    REFERENCES `system_resources` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `group_resources`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `group_resources` ;

CREATE TABLE IF NOT EXISTS `group_resources` (
  `system_group_id` INT UNSIGNED NOT NULL,
  `system_resources_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`system_group_id`, `system_resources_id`),
  INDEX `fk_system_group_has_system_resources_system_resources1_idx` (`system_resources_id` ASC),
  INDEX `fk_system_group_has_system_resources_system_group1_idx` (`system_group_id` ASC),
  UNIQUE INDEX `un_group_resources` (`system_group_id` ASC, `system_resources_id` ASC),
  CONSTRAINT `fk_system_group_has_system_resources_system_group1`
    FOREIGN KEY (`system_group_id`)
    REFERENCES `system_group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_system_group_has_system_resources_system_resources1`
    FOREIGN KEY (`system_resources_id`)
    REFERENCES `system_resources` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `system_group`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `system_group` (`id`, `group_name`) VALUES (1, 'SuperUser');
INSERT INTO `system_group` (`id`, `group_name`) VALUES (2, 'OwnerUser');

COMMIT;


-- -----------------------------------------------------
-- Data for table `system_user`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `system_user` (`id`, `username`, `password`, `name`, `email`, `avatar`, `create_date`, `update_date`, `delete_date`, `user_type`, `active`, `can_edit`, `can_login`, `can_use_web`, `can_use_api`, `system_group_id`) VALUES (1, 'ispti', '$2y$10$cFz.uVNrq0WcqgJSbAAFJ.26JaEh7lhyaS1JRNhkQbWU9YduEQMFu', 'Ispti Corp', 'test@user.com', 'admin.png', '2016-04-16 20:00:00', NULL, NULL, 'M', 1, 1, 1, 1, 1, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `system_resources`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `system_resources` (`id`, `name`, `permission`) VALUES (1, 'admin', 7);
INSERT INTO `system_resources` (`id`, `name`, `permission`) VALUES (2, 'admin', 0);

COMMIT;

