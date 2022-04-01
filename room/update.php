<?php

require_once "../_includes/bootstrap.inc.php";
session_start();

final class Page extends BaseDBPage{

    const STATE_FORM_REQUESTED = 1;
    const STATE_DATA_SENT = 2;
    const STATE_REPORT_RESULT = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private RoomModel $room;
    private int $state;
    private int $result;

    public function __construct()
    {
        parent::__construct();
        $this->title = "Room update";
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getState();

        if ($this->state === self::STATE_REPORT_RESULT) {
            if ($this->result === self::RESULT_SUCCESS) {
                $this->title = "Room update";
            } else {
                $this->title = "Room update failed";
            }
            return;
        }

        if ($this->state === self::STATE_DATA_SENT) {
            $this->room = RoomModel::getFromPost();
            if ($this->room->validate()) {
                //uložím
                if ($this->room->update()) {
                    $this->redirect(self::RESULT_SUCCESS);
                } else {
                    $this->redirect(self::RESULT_FAIL);
                }
            } else {
                $this->state = self::STATE_FORM_REQUESTED;
                $this->title = "Room update: Invalid data";
            }
        } else {
            $this->title = "Update room";
            $roomId = filter_input(INPUT_GET, "room_id", FILTER_VALIDATE_INT);
            if ($roomId){
                $this->room = RoomModel::getById($roomId);
                if (!$this->room)
                    throw  new RequestException(404);
            } else {
                throw  new RequestException(400);
            }
        }

    }


    protected function body(): string {
        if ($this->state === self::STATE_FORM_REQUESTED) {
            if($_SESSION["admin"]){
            return $this->m->render("roomForm", [
                "room"=>$this->room,
                "errors"=>$this->room->getValidationErrors(),
                "update"=>true
            ]);
            }else{
                return $this->m->render("reportFail",["data"=>"you're not admin","href"=>"./"]);
            }
        } elseif ($this->state === self::STATE_REPORT_RESULT) {
            if ($this->result === self::RESULT_SUCCESS) {
                return $this->m->render("reportSuccess", ["data"=>"Room update successfully","href"=>"./"]);
            } else {
                return $this->m->render("reportFail", ["data"=>"Room update failed. Please contact adiministrator or try again later.","href"=>"./"]);
            }

        }else{
            return"";
        }
    }

    private function getState() : void {
        //je už hotovo?
        $result = filter_input(INPUT_GET, "result", FILTER_VALIDATE_INT);
        if ($result === self::RESULT_SUCCESS) {
            $this->state = self::STATE_REPORT_RESULT;
            $this->result = self::RESULT_SUCCESS;
            return;
        } elseif ($result === self::RESULT_FAIL) {
            $this->state = self::STATE_REPORT_RESULT;
            $this->result = self::RESULT_FAIL;
            return;
        }

        //byl odeslán formulář
        $action = filter_input(INPUT_POST, "action");
        if ($action === "update") {
            $this->state = self::STATE_DATA_SENT;
            return;
        }

        $this->state = self::STATE_FORM_REQUESTED;
    }

    private function redirect(int $result) : void {
        //odkaz sám na sebe, bez query string atd.
        $location = strtok($_SERVER['REQUEST_URI'], '?');

        header("Location: {$location}?result={$result}");
        exit;
    }
}

(new Page())->render();