<?php

abstract class State
{
    private $remark;
    abstract public function goNext($patient): void;
    abstract public function displayUI($patient);
}

class NewPatient extends State
{

    function __construct($remark)
    {
        $this->remark = $remark;
    }

    public function goNext($patient): void
    {
        $patient->setState(new ExistingPatient("Existing Patient"));
    }

    public function displayUI($patient)
    {
        header("Location: ../../views/ExistingPatient/ExistingPatientForm.php");
        return;
    }
}

class ExistingPatient extends State
{

    function __construct($remark)
    {
        $this->remark = $remark;
    }

    public function goNext($patient): void
    {
        $patient->setState(new DischargedPatient("Discharged Patient"));
    }

    public function displayUI($patient)
    {
        header("Location: ../../views/ExistingPatient/ExistingPatientForm.php");
        return;
    }
}

class DischargedPatient extends State
{

    function __construct($remark)
    {
        $this->remark = $remark;
    }

    public function goNext($patient): void
    {
        $this->remark = "Patient has already been discharged";
    }

    public function displayUI($patient)
    {
        
        header("Location: ../../views/DischargedPatient/DischargedPatientForm.php");
        return;
    }
}
