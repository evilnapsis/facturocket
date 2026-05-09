<?php
class PaymentFormData {
	public static $tablename = "payment_form";

	public $id, $code, $name;

	public function __construct(){
		$this->code = "";
		$this->name = "";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (code,name) ";
		$sql .= "value (\"$this->code\",\"$this->name\")";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set code=\"$this->code\",name=\"$this->name\" where id=$this->id";
		return Executor::doit($sql);
	}

	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentFormData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentFormData());
	}
}
?>
