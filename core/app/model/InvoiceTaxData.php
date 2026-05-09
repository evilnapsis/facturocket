<?php
class InvoiceTaxData {
	public static $tablename = "invoice_tax";

	public $id, $invoice_item_id, $tax_id, $base, $rate, $amount, $type, $created_at;

	public function __construct(){
		$this->base = 0;
		$this->rate = 0;
		$this->amount = 0;
		$this->type = 1;
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (invoice_item_id,tax_id,base,rate,amount,type,created_at) ";
		$sql .= "value ($this->invoice_item_id,$this->tax_id,$this->base,$this->rate,$this->amount,$this->type,$this->created_at)";
		return Executor::doit($sql);
	}

	public static function getAllByItemId($id){
		$sql = "select * from ".self::$tablename." where invoice_item_id=$id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new InvoiceTaxData());
	}

	public function getTax(){ return TaxData::getById($this->tax_id); }
}
?>
