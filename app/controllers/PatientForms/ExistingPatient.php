<?php
include '../../views/layouts/docmenu.php';
// include '../../views/HeaderAndFooter/header.php';
include '../../models/DatabaseConnection/Database.php';
//include '../../models/Users.php';
include '../../classes/Patient.php';
include '../../classes/Test.php';

if (!(isset($_SESSION))){
  session_start();
  if (!(isset($_SESSION["username"]))){
    header("Location: ../register/login");
    return;
  }
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="../../../js/jQuery-2.2.4.min.js"></script>
    <script src="../../../bootstrap/js/bootstrap.min.js"></script>
    <link rel = "stylesheet" href = "../../../bootstrap/css/bootstrap.min.css" integrity="" crossorigin="anonymous">
    <link rel = "stylesheet" href = "../../../css/navNsideStyles.css">
    <link rel = "stylesheet" href = "../../../css/mainStyles.css">
    <title> </title>
  </head>
  <body class = 'mainbody'>
    <div class="container">
  <?php
$medical = Database::getInstance();
if (isset($_POST["test_submit"])){
  if (isset($_POST['tests'])){
    $tests=$_POST['tests'];
    if (!(empty($tests)))
    {
        foreach($tests as $test)
        {
          $class_name = ucfirst($test);
          $command = new $class_name;
          $command->execute($medical,array($_SESSION["regNo"], date('Y-m-d')));
          //$medical->enterData($test, array('patient_id','sdate'), array($_SESSION["regNo"], date('Y-m-d')));
        }
    }
  }
}

if(isset($_POST['regNo'])){
  $regNo = $_POST["regNo"];
  $_SESSION["regNo"] = $_POST['regNo'];
  //echo $regNo;
} 
else if (isset($_SESSION['regNo'])){
  $regNo = $_SESSION["regNo"];
} 

$arr = explode ("/", $regNo);  //get the patient id from the registration number
$patientID = $arr[1];
$_SESSION["PatientID"] = $patientID;
$columns = array('RegNo', 'FullName', 'Gender', 'FullAddress', 'DateOfBirth', 'Disease',  'BedNo','ContactNo');
//$results =  $medical->retrieveData("patients", $columns, $regNo);
$results =  $medical->joinPatientWithDiagnosis("patients",$columns, "PatientID", $patientID);
if (($results)) {
      $regNo = $results['RegNo'];
      $diagnosis =  $results['Disease'];
      $name = $results['FullName'];
      $gender =  $results['Gender'];
      $address =  $results['FullAddress'];

      $dob =  $results['DateOfBirth'];
      $bday = new DateTime($dob);
      $today = new DateTime();
      $diff = $today->diff($bday);
      $y = $diff->y;
      $m = $diff->m;
      $d = $diff->d;
      if ($y!=0){
        $age = $y . " year/s";
      }
      else if ($m!=0){
        $age = $m . " month/s ".$d." day/s";
      }
      else {
        $age = $d . " day/s";
      }

      $contact = $results['ContactNo'];
      $bedNo = $results['BedNo'];
      if ($bedNo==''){
        $admission="Not admitted";
      }
      else{
        $admission = "Admitted";
      }

      $patient = new Patient($regNo, $name, $age, $address,$diagnosis,$dob,$gender,$admission, $bedNo, $contact,"Existing");
      if (isset($_SESSION["Patient"]) ){
        unset($_SESSION['Patient']);
      }
      $_SESSION["Patient"] = $patient;
      header("Location: ../../views/ExistingPatient/ExistingPatientForm.php");
      //$patient->displayUI();
    //include '../../views/ExistingPatient/ExistingPatientForm.php';
}

else{
  $_SESSION['error'] = "Registration number not found";
  header("Location: ../../views/Searching.php");
  return;
}
          ?>
          </div>
    </body>
</html>