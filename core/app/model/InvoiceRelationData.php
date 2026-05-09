<?php
class InvoiceRelationData {
	public static $tablename = "invoice_relation";
    public $id, $invoice_id, $relation_type_id, $related_uuid;
	public function __construct(){
		$this->id = "";
		$this->invoice_id = "";
		$this->relation_type_id = "";
		$this->related_uuid = "";
	}
	public function add(){
		$sql = "insert into ".self::$tablename." (invoice_id, relation_type_id, related_uuid) ";
		$sql .= "value (\"$this->invoice_id\",\"$this->relation_type_id\",\"$this->related_uuid\")";
		return Executor::doit($sql);
	}
	public static function getAllByInvoiceId($id){
		$sql = "select * from ".self::$tablename." where invoice_id=$id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new InvoiceRelationData());
	}
}
?>
