<?php
class RoomModel
{
public ?int $room_id;
public string $name = "";
public string $no = "";
public ?string $phone = null;


private array $validationErrors = [];

public function getValidationErrors(): array
{
return $this->validationErrors;
}

public function __construct()
{
}

public function insert() : bool {

$sql = "INSERT INTO room (name, no, phone) VALUES (:name, :no, :phone)";

$stmt = DB::getConnection()->prepare($sql);
$stmt->bindParam(':name', $this->name);
$stmt->bindParam(':no', $this->no);
$stmt->bindParam(':phone', $this->phone);

return $stmt->execute();
}

public function update() : bool
{
$sql = "UPDATE room SET name=:name, no=:no, phone=:phone WHERE room_id=:room_id";

$stmt = DB::getConnection()->prepare($sql);
$stmt->bindParam(':room_id', $this->room_id);
$stmt->bindParam(':name', $this->name);
$stmt->bindParam(':no', $this->no);
$stmt->bindParam(':phone', $this->phone);

return $stmt->execute();
}
public static function getEmployees($roomId){
    $stmt = DB::getConnection()->prepare("SELECT surname, name, wage, employee_id FROM `employee` WHERE `room`=:room_id");
    $stmt->bindParam(':room_id', $roomId);
    $stmt->execute();
    return $stmt;
}
public static function avgSalary($roomId){
    $stmt = DB::getConnection()->prepare("SELECT ROUND(AVG(wage),0) AS salary FROM `employee` WHERE `room`=:room_id");
    $stmt->bindParam(':room_id', $roomId);
    $stmt->execute();
    $salary = $stmt->fetch();
    return $salary->salary;
}
public static function getKeys($roomId){
    $stmt = DB::getConnection()->prepare("SELECT k.employee, k.room, e.name, e.surname, e.employee_id FROM `key` k INNER JOIN employee e ON k.employee=e.employee_id WHERE k.room=:room_id");
    $stmt->bindParam(':room_id', $roomId);
    $stmt->execute();
    return $stmt;
}
public static function getById($roomId) : ?self
{
$stmt = DB::getConnection()->prepare("SELECT * FROM `room` WHERE `room_id`=:room_id");
$stmt->bindParam(':room_id', $roomId);
$stmt->execute();

$record = $stmt->fetch();

if (!$record)
return null;

$model = new self();
$model->room_id = $record->room_id;
$model->name = $record->name;
$model->no = $record->no;
$model->phone = $record->phone;
return $model;
}

public static function getAll($orderBy = "name", $orderDir = "ASC") : PDOStatement
{

$stmt = DB::getConnection()->prepare("SELECT * FROM `room` ORDER BY `{$orderBy}` {$orderDir}");
$stmt->execute();
return $stmt;

}

public static function deleteById(int $room_id) : bool
{
$sql2= "SELECT * FROM employee";
$stmt2= DB::getConnection()->prepare($sql2);
$canDel = true;
foreach ($stmt2->execute()->fetch as $emp){
    if($emp->room===$room_id){
    $canDel=false;
    }
}
if($canDel){
    $sql = "DELETE FROM `key` WHERE room=:room_id";
    $stmt = DB::getConnection()->prepare($sql);
    $stmt->bindParam(':room_id', $room_id);
    $keyDel = $stmt->execute();
    $sql = "UPDATE employee SET room = NULL WHERE room=:room_id";
    $stmt = DB::getConnection()->prepare($sql);
    $stmt->bindParam(':room_id', $room_id);
    $empDel = $stmt->execute();
    $sql = "DELETE FROM room WHERE room_id=:room_id";
    $stmt = DB::getConnection()->prepare($sql);
    $stmt->bindParam(':room_id', $room_id);
    $roomDel = $stmt->execute();
    if($roomDel && $keyDel && $empDel){
        return true;
    }else{
        return false;
    }
}else
    return false;

}

public function delete() : bool
{
return self::deleteById($this->room_id);
}

public static function getFromPost() : self {
$room = new RoomModel();

$room->room_id = filter_input(INPUT_POST, "room_id", FILTER_VALIDATE_INT);
$room->name = filter_input(INPUT_POST, "name");
$room->no = filter_input(INPUT_POST, "no");
$room->phone = filter_input(INPUT_POST, "phone");

return $room;
}

public function validate() : bool {
$isOk = true;
$errors = [];

if (!$this->name){
$isOk = false;
$errors["name"] = "Room name cannot be empty";
}

if (!$this->no){
$isOk = false;
$errors["no"] = "Room number cannot be empty";
}
if ($this->phone === ""){
$this->phone = null;
}

$this->validationErrors = $errors;
return $isOk;
}
}