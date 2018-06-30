<?php 
	require_once('./config.php');

	$dbh = new PDO("mysql:host=$host", $user, $password);
	
	try {
		//создаем базу данных
        $dbh->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET $charset COLLATE utf8_general_ci") 
        or die(print_r($dbh->errorInfo(), true));

    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }
	unset($dbh);
	
	$dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
	try {
        //создание таблицы
        $dbh->exec("CREATE TABLE IF NOT EXISTS `message` ( 
		               `ID` INT NOT NULL AUTO_INCREMENT, 
					   `Name` VARCHAR(40) NOT NULL, 
					   `SecondName` VARCHAR(40) NOT NULL, 
					   `Email` VARCHAR(256) NOT NULL, 
					   `Message` TEXT NOT NULL,
					PRIMARY KEY (`ID`))");

    } catch (PDOException $e) {
        die("DB ERROR: ". $e->getMessage());
    }

	echo "Создана база данных: $dbname \n";
	echo "Создана таблица: message";