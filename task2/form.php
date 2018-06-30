<?php 

    if($_SERVER['REQUEST_METHOD'] != 'POST') die("Ошибка! Заполните форму");
	
	$pattern = "/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i";
	
	if(preg_match($pattern, $_POST["email"])) die("Ошибка! Введите корректный email");
	
	require_once('./config.php');

	$dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
	
	try {
		$sth = $dbh->prepare("INSERT INTO `message`(`Name`,`SecondName`,`Email`,`Message`) 
							  VALUES ( :name, :secondname, :email, :message)");
						  
		$sth->bindParam(':name',$_POST["name"]);
		$sth->bindParam(':secondname', $_POST["secondname"]);
		$sth->bindParam(':email', $_POST["email"]);
		$sth->bindParam(':message', $_POST["message"]);
		$sth->execute();

    } catch (PDOException $e) {
        die("ERROR: ". $e->getMessage());
    }
