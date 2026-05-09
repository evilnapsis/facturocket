<?php
class RelationTypeData {
	public static $tablename = "relation_type";
    public $id, $code, $name;
	public function __construct(){
		$this->id = "";
		$this->code = "";
		$this->name = "";
	}
	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new RelationTypeData());
	}
	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new RelationTypeData());
	}
}
?>
