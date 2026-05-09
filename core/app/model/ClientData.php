<?php
class ClientData {
	public static $tablename = "client";

	public $id, $rfc, $name, $email, $address, $zip_code, $tax_regime_id, $cfdi_use_id, $created_at;

	public function __construct(){
		$this->rfc = "";
		$this->name = "";
		$this->email = "";
		$this->address = "";
		$this->zip_code = "";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (rfc,name,email,address,zip_code,tax_regime_id,cfdi_use_id,created_at) ";
		$sql .= "value (\"$this->rfc\",\"$this->name\",\"$this->email\",\"$this->address\",\"$this->zip_code\",$this->tax_regime_id,$this->cfdi_use_id,$this->created_at)";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set rfc=\"$this->rfc\",name=\"$this->name\",email=\"$this->email\",address=\"$this->address\",zip_code=\"$this->zip_code\",tax_regime_id=$this->tax_regime_id,cfdi_use_id=$this->cfdi_use_id where id=$this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ClientData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new ClientData());
	}

	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		return Executor::doit($sql);
	}

	public function getTaxRegime(){ return TaxRegimeData::getById($this->tax_regime_id); }
	public function getCfdiUse(){ return CfdiUseData::getById($this->cfdi_use_id); }
}
?>
