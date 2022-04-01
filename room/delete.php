<?php

require_once "../_includes/bootstrap.inc.php";
session_start();

    final class Page extends BaseDBPage{

//    const STATE_FROM_REQUESTED = 1;
//    const STATE_DATA_SENT = 2;
        const STATE_REPORT_RESULT = 3;
        const STATE_DELETE_REQUESTED = 4;

        const RESULT_SUCCESS = 1;
        const RESULT_FAIL = 2;

//    private RoomModel $room;
        private int $state;
        private int $result;

        public function __construct()
        {
            parent::__construct();
            $this->title = "Room delete";
        }

        protected function setUp(): void
        {
            parent::setUp();

            $this->getState();

            if ($this->state === self::STATE_REPORT_RESULT) {
                if ($this->result === self::RESULT_SUCCESS) {
                    $this->title = "Room created";
                } else {
                    $this->title = "Room creation failed";
                }
                return;
            }

            if ($this->state === self::STATE_DELETE_REQUESTED) {
                $roomId = filter_input(INPUT_POST, "room_id", FILTER_VALIDATE_INT);
                if ($roomId){
                    //smažu
                    if (RoomModel::deleteById($roomId)) {
                        $this->redirect(self::RESULT_SUCCESS);
                    } else {
                        $this->redirect(self::RESULT_FAIL);
                    }
                } else {
                    throw new RequestException(400);
                }

            }

        }


        protected function body(): string {
            if($_SESSION["admin"]) {
                if ($this->state === self::STATE_REPORT_RESULT) {
                    if ($this->result === self::RESULT_SUCCESS) {
                        return $this->m->render("reportSuccess", ["data" => "Room deleted successfully","href"=>"./"]);
                    } else {
                        return $this->m->render("reportFail", ["data" => "Room delete failed. Please contact adiministrator or try again later.","href"=>"./"]);
                    }
                }
            }else {
                return $this->m->render("reportFail",["data"=>"you're not admin","href"=>"./"]);
            }
            return "";
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

            $this->state = self::STATE_DELETE_REQUESTED;
        }

        private function redirect(int $result) : void {
            $location = strtok($_SERVER['REQUEST_URI'], '?');

            header("Location: {$location}?result={$result}");
            exit;
        }
    }

(new Page())->render();
