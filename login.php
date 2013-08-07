<?php
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,
        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
include_once '.config.php';
try {
 $dbh = new PDO($dsn, $user, $password);
 } catch (PDOException $e) {
    echo 'Подключение не удалось: ' . $e->getMessage();
}
$login=$_POST['login'];
$password=$_POST['password'];
$logining=$dbh->prepare("SELECT password FROM users WHERE login=:login ;");
$logining->bindParam(':login', $login, PDO::PARAM_STR);
$logining->execute();
$db_password=$logining->fetch(PDO::FETCH_ASSOC)["password"];
$create='CREATE TABLE IF NOT EXISTS sessions (
        ID INT,
        token VARCHAR(36),
        expires DATE,
        PRIMARY KEY(token));';
include_once "password.php";
if ( password_verify ($password , $db_password)) {
        $token = gen_uuid();
        $dbh->query($create);
        $logining=$dbh->prepare("SELECT * FROM users WHERE login=:login;");
        $logining->bindParam(':login', $login, PDO::PARAM_STR);
        $logining->execute();
        $user=$logining->fetch(PDO::FETCH_ASSOC);
        $ID=$user["ID"];
        $table="INSERT INTO sessions (ID, token, expires)
        VALUES ('$ID' , '$token', DATE_ADD(NOW(), INTERVAL 30 day));";
        $dbh->query($table);
        echo "Success.";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
} else {
    echo "Invalid Password";
}
?>
