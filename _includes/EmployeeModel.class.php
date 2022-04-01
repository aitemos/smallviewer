<?php
class EmployeeModel
{public ?int $employee_id;
public string $name = "";
public string $surname = "";
public string $job = "";
public ?int $wage;
public ?int $room;
public string $roomname;
public ?array $keys=[];

private array $validationErrors = [];

public function getValidationErrors(): array
{
return $this->validationErrors;
}

public function __construct()
{

}

public function insert() : bool
{

    $sql = "INSERT INTO employee (name,surname,job , wage, room) VALUES (:name,:surname, :job, :wage,:room)";

    $stmt = DB::getConnection()->prepare($sql);

    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':surname', $this->surname);
    $stmt->bindParam(':job', $this->job);
    $stmt->bindParam(':room', $this->room);
    $stmt->bindParam(':wage', $this->wage);
    $emp = $stmt->execute();
    $sql = "SELECT employee_id FROM employee WHERE name=:name AND surname=:surname";
    $stmt = DB::getConnection()->prepare($sql);
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':surname', $this->surname);
    $stmt->execute();
    $id = $stmt->fetch();
    foreach ($this->keys as $key) {
        $sql = "INSERT INTO `key` (employee,room) VALUES (:employee,:room)";
        $stmt = DB::getConnection()->prepare($sql);
        $stmt->bindParam(':employee', $id->employee_id);
        $stmt->bindParam(':room', $key);
        $keyUp = $stmt->execute();
    }
    if ($keyUp && $emp) {
        return true;
    } else
        return false;
}

public function update() : bool
{
$sql = "UPDATE employee SET name=:name,surname=:surname, job=:job, room=:room, wage=:wage WHERE employee_id=:employee_id";
$stmt = DB::getConnection()->prepare($sql);
$stmt->bindParam(':employee_id', $this->employee_id);
$stmt->bindParam(':name', $this->name);
$stmt->bindParam(':room',$this->room);
$stmt->bindParam(':job', $this->job);
$stmt->bindParam(':surname', $this->surname);
$stmt->bindParam(':wage', $this->wage);
$empUp=$stmt->execute();
$sql = "DELETE FROM `key` WHERE employee=:employee_id";
$stmt= DB::getConnection()->prepare($sql);
$stmt->bindParam(':employee_id',$this->employee_id);
$stmt->execute();
foreach($this->keys as $key){
$sql="INSERT INTO `key` (employee,room) VALUES (:employee,:room)";
$stmt=DB::getConnection()->prepare($sql);
$stmt->bindParam(':employee',$this->employee_id);
$stmt->bindParam(':room',$key);
$keyUp = $stmt->execute();
}
if($keyUp && $empUp){
    return true;
}else
    return false;

}

public static function getById($employeeId) : ?self
{
$stmt = DB::getConnection()->prepare("SELECT wage, employee_id, employee.name AS empname, surname, room.name AS roomname, room.phone AS phone, job ,room_id FROM employee INNER JOIN room ON employee.room=room.room_id WHERE employee_id=:employee_id ");
$stmt->bindParam(':employee_id', $employeeId);
$stmt->execute();
$record = $stmt->fetch();
if (!$record)
return null;

$model = new self();
$model->employee_id = $record->employee_id;
$model->name = $record->empname;
$model->room = $record->room_id;
$model->surname = $record->surname;
$model->roomname = $record->roomname;
$model->job = $record->job;
$model->wage = $record->wage;
return $model;
}

public static function getAll($orderBy = "surname", $orderDir = "ASC") : PDOStatement
{

$stmt = DB::getConnection()->prepare("SELECT employee_id, employee.name AS empname, surname, room.name AS roomname, room.phone AS phone, job ,room_id FROM employee INNER JOIN room ON employee.room=room.room_id ORDER BY {$orderBy} {$orderDir}");
$stmt->execute();
    return $stmt;

}
public static function getKeys($employeeId){
        $stmt = DB::getConnection()->prepare("SELECT r.name, r.room_id FROM `key` k INNER JOIN room r ON k.room=r.room_id WHERE k.employee=:employee_id");
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        return $stmt;
    }
public static function deleteById(int $employee_id) : bool
{
$sql = "DELETE FROM `key` WHERE employee=:employee_id";
$stmt = DB::getConnection()->prepare($sql);
$stmt->bindParam('employee_id',$employee_id);
$empDel = $stmt->execute();
$sql = "DELETE FROM employee WHERE employee_id=:employee_id";
$stmt = DB::getConnection()->prepare($sql);
$stmt->bindParam(':employee_id', $employee_id);
$keyDel = $stmt->execute();
if($empDel && $keyDel){
    return true;
}else{
    return false;
}
}

public function delete() : bool
{
return self::deleteById($this->employee_id);
}

public static function getFromPost() : self {
$employee = new EmployeeModel();

$employee->employee_id = filter_input(INPUT_POST, "employee_id", FILTER_VALIDATE_INT);
$employee->name = filter_input(INPUT_POST, "name");
$employee->job = filter_input(INPUT_POST, "job");
$employee->surname = filter_input(INPUT_POST, "surname");
$employee->room = $_POST['room'];
$employee->wage = filter_input(INPUT_POST, "wage",FILTER_VALIDATE_INT);
if($_POST['keys']===null){
    $_POST['']="";
}
$employee->keys = $_POST['keys'];

return $employee;
}

public function validate() : bool {
$isOk = true;
$errors = [];

if (!$this->name){
$isOk = false;
$errors["name"] = "Employee name cannot be empty";
}
if(!$this->surname){
    $isOk = false;
    $errors["surname"] = "Employee name cannot be empty";
}
if (!$this->job){
$isOk = false;
$errors["job"] = "Job cannot be empty";
}
if(!$this->wage){
    $isOk = false;
    $errors["wage"]= "Wage cannot be empty";
}


$this->validationErrors = $errors;
return $isOk;
}
}