<?php
 require 'Classes/PHPExcel/IOFactory.php';

 $nomServer = "localhost";
 $username  = "root";
 $password  = "";
 $dbname    = "testexcel";

 if (isset($_POST['upload'])){
     $inputfilename = $_FILES['file']['tmp_name'];
     $exceldata     = array();

     $con = mysqli_connect($nomServer,$username,$password,$dbname);

     if(!$con){
        die("Connection échoué ". mysqli_connect_error());
     }
     try{
        $inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
        $objReader     = PHPExcel_IOFactory::createReader($inputfiletype);
        $objPHPExcel   = $objReader->load($inputfilename);
         }catch(Exception $e){
            die('Erreur sur la chargement du fichier"'.pathinfo($inputfilename,PATHINFO_BASENAME).'": '.$e->getMessage());
     }

     $feuille   = $objPHPExcel->getSheet(0); //feuille (sheet)
     $range     = $feuille->getHighestRow(); //row premier rangée
     $colonne   = $feuille->getHighestColumn(); //premier colonne

     for($row = 1; $row<=$range;$row ++){
        $rowdata = $feuille ->rangeToArray('A'.$row.':'.$colonne.$row,NULL,TRUE,FALSE);

        $sql ="INSERT INTO excel (prenom,nom,adresse) VALUES ('".$rowdata[0][0]."','".$rowdata[0][1]."','".$rowdata[0][2]."')";

    if(mysqli_query($con,$sql)){
        $exceldata[] = $rowdata[0];
    }else{
        echo "Erreur: ".$sql ."<br>".mysqli_error($con);
    }
     }

     echo "<table border='1'>";
        foreach($exceldata as $index => $excelraw){
            echo "<tr>";
                foreach($excelraw as $excelcolonne){
                    echo "<td>".$excelcolonne."<td>";
                }
            echo "<tr>";
        }
    echo "</table>";
mysqli_close($con);
 }
 ?>
  <!DOCTYPE html>
  <html>
  <head>
      <meta charset="utf-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Importation excel</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
        <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" name="upload" value="upload">
        </form>
  </body>
  </html>