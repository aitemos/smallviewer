<?php

require_once "../_includes/bootstrap.inc.php";
session_start();
final class Page extends BaseDBPage{
    public function __construct()
    {
        parent::__construct();
        $this->title = "Employee listing";
    }

    protected function body(): string
    {
        $this->admin="";
        if(!$_SESSION["admin"]){
            $this->admin = "disabled";
        }
        if($_SESSION["logged"]){
        return $this->m->render(
            "employeeList",
            ["employees" => EmployeeModel::getAll(), "employeeDetailName" => "employeeDetail.php","admin"=>$this->admin]
        );}else{
            return $this->m->render("reportFail",["data"=>"you re not logged","href"=>"../index"]);
        }
    }
}

(new Page())->render();
