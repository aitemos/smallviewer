<?php
session_start();
require_once "../_includes/bootstrap.inc.php";


    final class Page extends BaseDBPage{
        public string $admin;
        public function __construct()
        {
            parent::__construct();
            $this->title = "Room listing";
        }

        protected function body(): string
        {
            $this->admin="";
            if(!$_SESSION["admin"]){
                $this->admin = "disabled";
            }

            if($_SESSION["logged"]){
            return $this->m->render(
                "roomList",
                ["rooms" => RoomModel::getAll(), "roomDetailName" => "roomDetail.php","admin"=>$this->admin]
            );
            }else{
                return $this->m->render("reportFail",["data"=>"you're not logged","href"=>"../index"]);
            }


        }
    }

(new Page())->render();

