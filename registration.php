<?php
include_once '.config.php';
try {
 $dbh = new PDO($dsn, $user, $password);
 } catch (PDOException $e) {
    echo 'Подключение не удалось: ' . $e->getMessage();
}
$create='CREATE TABLE IF NOT EXISTS users (
ID INT NOT NULL AUTO_INCREMENT,
login TEXT,
password TEXT,
nick TEXT,
interests TEXT,
birthdate TEXT,
PRIMARY KEY(id));';
$dbh->query($create);
$nick=$_POST['nick'];
$login=$_POST['login'];
$interests=$_POST['interests'];
$birthdate=$_POST['birthdate'];
$password=$_POST['password'];
include_once "password.php";
$cr_password=password_hash($password, PASSWORD_DEFAULT);
$screening=$dbh->prepare("SELECT login from users WHERE login=:login;");
$screening->bindParam(':login', $login, PDO::PARAM_STR);
$screening->execute();
$sc_login=$screening->fetch(PDO::FETCH_ASSOC);
if (!empty($sc_login)) {
  die("Login “ $login ” exists");
}
$registration= $dbh->prepare('INSERT INTO users(login, password,nick, interests, birthdate)
VALUES ( :login, :cr_password,:nick, :interests, :birthdate) ;');
$registration->bindParam(':login', $login, PDO::PARAM_STR);
$registration->bindParam(':nick', $nick, PDO::PARAM_STR);
$registration->bindParam(':interests', $interests, PDO::PARAM_STR);
$registration->bindParam(':birthdate', $birthdate, PDO::PARAM_STR);
$registration->bindParam(':cr_password', $cr_password, PDO::PARAM_STR);
$registration->execute();
echo "Success";
?>
