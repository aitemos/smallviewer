<?php

require_once "../_includes/bootstrap.inc.php";
session_start();
final class Page extends BaseDBPage{
    public int $employeeId;
    public function __construct()
    {

        parent::__construct();
        $this->title = "Employee Detail";
    }

    protected function body(): string
    {
        if($_SESSION["logged"]){
        $this->employeeId = filter_input(INPUT_GET,"employee_id",FILTER_VALIDATE_INT);
        if($this->employeeId==null){
            throw new RequestException(400);
        }
        return $this->m->render(
            "employeeDetail",
            ["employee" => EmployeeModel::getById($this->employeeId), "employeeDetailName" => "employeeDetail.php","keys"=>EmployeeModel::getKeys($this->employeeId)]
        );
        }else{
            return $this->m->render("reportFail",["data"=>"you're not logged","href"=>"../login"]);
        }
    }
}

(new Page())->render();
