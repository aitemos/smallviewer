<?php
include_once("_includes/bootstrap.inc.php");
session_start();

final class Page extends BaseDBPage{
    public string $newPass="";
    public string $sql = "SELECT password FROM employee WHERE employee_id=:id";
    public function __construct()
    {
        parent::__construct();
        $this->title = "Password change";
    }

    protected function body(): string
    {
        $stmt = DB::getConnection()->prepare($this->sql);
        $stmt->bindParam(':id',$_SESSION['id']);
        $stmt -> execute();
        $login = $stmt->fetch();
        $newPass= hash('sha256',$_POST["password"]);
        if($login->password === hash('sha256',$_POST["oldPass"])){
            $sql = "UPDATE employee SET password = :password WHERE employee_id=:id";
            $stmt =DB::getConnection()->prepare($sql);
            $stmt->bindParam('password',$newPass);
            $stmt->bindParam('id',$_SESSION["id"]);
            $stmt->execute();
            return $this->m->render("reportSuccess",["data"=>"password changed","href"=>"./profile"]);
        }else
            return $this->m->render("reportFail",["data"=>"wrong old password","href"=>"./profile"]);

    }
}(new Page())->render();




