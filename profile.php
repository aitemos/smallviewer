<?php
session_start();
require_once "./_includes/bootstrap.inc.php";

    final class Page extends BaseDBPage{

        public function __construct()
        {
            parent::__construct();
            if($_SESSION["name"] && $_SESSION["surname"]){
                $this->title = "Profile ".$_SESSION["surname"]." ".$_SESSION["name"];
            }
        }

        protected function body(): string
        {
            if($_SESSION["logged"]){
            return $this->m->render(
                "profile",
                ["name"=>$_SESSION["name"],"surname"=>$_SESSION["surname"]]
            );
            }else{
                return $this->m->render(
                    "reportFail",
                    ["data"=>"you re not logged  "]
                    );
            }

        }
    }
    (new Page())->render();

