<?php
//include('zkouska.html');
//echo("Funguje to");
session_start();

function odhlasit(){
    unset($_SESSION["login"]);
    unset($_SESSION["jmeno"]);
    unset($_SESSION["prijmeni"]);
    unset($_SESSION["Foto"]);
    session_destroy();
    echo("Uživatel byl ze systému odhlášen... Pro nové přihlášení pokračujte <A href='zkouska.html'>ZDE</A>");
    }

    
// ************************* EXPIRACE PRIHLASENI ***************************
if(isset($_SESSION["caspristupu"]) && (($_SESSION["caspristupu"]+900)<time())) odhlasit(); else $_SESSION["caspristupu"]=time();
    


//********************************Prihlásit************************************/
if(isset($_POST["submit"]) && $_POST["submit"]=="PŘIHLÁSIT")
{

if(!isset($_POST["login"]) || !$_POST["login"]) die("Nezadane login");
if(!isset($_POST["heslo"]) || !$_POST["heslo"]) die("Nezadane heslo");

$mysqli = @mysqli_connect('127.0.0.1', 'root', '', 'prog');
if (!$mysqli) die("Nelze se připojit");
$vysledek = mysqli_query($mysqli,"SELECT * from osoba where login='".$_POST["login"]."' and Password = '".hash("sha512",$_POST["heslo"])."'");
$zaznam = mysqli_fetch_array($vysledek);
if($zaznam) {

echo("Uživatel nalezen: ".$zaznam["Name"]." ".$zaznam["Surename"]);
$_SESSION["jmeno"] =  $zaznam["Name"];
$_SESSION["prijmeni"]= $zaznam["Surename"];
$_SESSION["login"] =$zaznam["Login"];
$_SESSION["caspristupu"] = time();
$_SESSION["Foto"] = $zaznam["Pic"];



if(isset($_GET["logout"]))
{
odhlasit();
} 

	else		

// ********** VYPIS INFORMACI O PRIHLASENI UZIVATELE PLUS MOZNOST JEHO ODHLASENI *********
if(isset($_SESSION["login"]))
{
 echo("<HR>Jste přihlášen do systému pod jménem <b>".$_SESSION["jmeno"]." ".$_SESSION["prijmeni"]."</b> a uživatelským jménem <b>".$_SESSION["login"]."</b><BR>");
 echo("Vaše fotografie:<br><IMG src='".$_SESSION["Foto"]."'><br>");
 echo("<A href='program.php?logout=true'>[ Odhlásit uživatele ]</A>");

  }
  
  else echo("Pro přihlášení uživatele přejděte <A href='zkouska.html'>na hlavní stránku</A>.");

}
else echo("Špatně zadané přihlašovací jméno či heslo!");
die; 
}


//******************************Registrace**************************************/
if(isset($_POST["submit"]) && $_POST["submit"]=="ZAREGISTROVAT")
{

if(!isset($_POST["jmeno"]) || !$_POST["jmeno"]) die("Nezadane jmeno");
if(!isset($_POST["prijmeni"]) || !$_POST["prijmeni"]) die("Nezadane přijmení");
if(!isset($_POST["email"]) || !$_POST["email"] || !filter_var ($_POST["email"],FILTER_VALIDATE_EMAIL)) die("Špatný formát emailu");

if(!isset($_POST["telefon"]) || !$_POST["telefon"] || !preg_match('/^\+420\s[0-9]{3}\s[0-9]{3}\s[0-9]{3}/', $_POST["telefon"])) die("Špatný formát telefonu");
if(!isset($_POST["pohlavi"]) || !$_POST["pohlavi"]) die("Nezadane pohlavi");
if(!isset($_POST["login"]) || !$_POST["login"]) die("Nezadane login");
if(!isset($_POST["heslo"]) || !$_POST["heslo"]) die("Nezadane heslo");

$Soubor = $_FILES["foto"]["tmp_name"];
switch(exif_imagetype($Soubor))
{
case IMAGETYPE_BMP: $image=imagecreatefrombmp($Soubor);break;
case IMAGETYPE_GIF: $image=imagecreatefromgif($Soubor);break;
case IMAGETYPE_PNG: $image=imagecreatefrompng($Soubor);break;
case IMAGETYPE_JPEG: $image=imagecreatefromjpeg($Soubor);break;
default: die("Nepodporovaný formát obrázku.<br><br>");
}
$FotoCesta= "./foto/".$_POST["login"]."-".$_POST["prijmeni"].".jpg";
$resolution = getimagesize($Soubor);
if($resolution [0]>640 || $resolution [1]>480) {


    $x_proport = 640 ;  //int $dst_x,
    $y_proport = round($x_proport / ($resolution[0] / $resolution[1])); //int $dst_y,
    $imageresized=imagecreatetruecolor($x_proport,$y_proport);
    imagecopyresized($imageresized,$image,0,0,0,0,$x_proport,$y_proport,$resolution[0],$resolution[1]);
    imagejpeg($imageresized,$FotoCesta,75);
     } else imagejpeg($image,$FotoCesta,75);



$mysqli = @mysqli_connect('127.0.0.1', 'root', '', 'prog');
if (!$mysqli) die("Nelze se připojit");
/* Set the desired charset after establishing a connection */

$vysledek=mysqli_query($mysqli,"INSERT INTO osoba (Name,Surename,Email,Sex,Phone,Login,Password,Pic) 
VALUES('".$_POST["jmeno"]."','".$_POST["prijmeni"]."', '".$_POST["email"]."','".$_POST["pohlavi"]."','".$_POST["telefon"]."','".$_POST["login"]."','".hash("sha512",$_POST["heslo"])."','".$FotoCesta."')");

if($vysledek) echo("Zaměstnanec úspěšně zaregistrován.<br><br>");
 else die("Chyba na SQL serveru (".mysqli_error($mysqli).")!");





//move_uploaded_file($Soubor,$FotoCesta);

 /* Prepare an insert statement */
//$stmt = $mysqli->prepare('INSERT INTO osoba (Name,Surename,Email,Sex,Phone,Login,Password) VALUES (?,?,?,?,?,?,?)');

/* Execute the statement */
//$stmt->execute(['".$_POST["jmeno"]."','".$_POST["prijmeni"]."', '".$_POST["email"]."','".$_POST["pohlavi"]."','".$_POST["telefon"]."','".$_POST["login"]."','".hash("sha512",$_POST["heslo"])."']);
//var_dump($stmt->execute);
/* Retrieve all rows from Osoba */
//$query = 'SELECT *  FROM osoba';
//$result = $mysqli->query($query);
//while ($row = $result->fetch_row()) {
//   printf("%s (%s,%s)\n", $row[0], $row[1], $row[2],$row[3], $row[4], $row[5],$row[6]);
//}

echo("Vše OK");

}
else die ("Táhni");


?>
