<?php

require_once "../_includes/bootstrap.inc.php";
session_start();

final class Page extends BaseDBPage{
    public int $roomId;
    public function __construct()
    {
        parent::__construct();
        $this->title = "Room Detail";
    }

    protected function body(): string
    {

        $this->roomId = filter_input(INPUT_GET,"room_id",FILTER_VALIDATE_INT);
        if($this->roomId==null){
            throw new RequestException(400);
        }
        if($_SESSION["logged"]){
        return $this->m->render(
            "roomDetail",
            ["room" => RoomModel::getById($this->roomId),"employees"=>RoomModel::getEmployees($this->roomId),
                "keys"=>RoomModel::getKeys($this->roomId),"salary"=>RoomModel::avgSalary($this->roomId)]
        );
        }else{
            return $this->m->render("reportFail",["data"=>"you're not logged","href"=>"../index"]);
        }


    }
}

(new Page())->render();

