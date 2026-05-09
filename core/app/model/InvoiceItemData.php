<?php
class InvoiceItemData {
	public static $tablename = "invoice_item";

	public $id, $invoice_id, $product_id, $description, $quantity, $price, $discount, $total, $tax_object_id, $created_at;

	public function __construct(){
		$this->description = "";
		$this->quantity = 1;
		$this->price = 0;
		$this->discount = 0;
		$this->total = 0;
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (invoice_id,product_id,description,quantity,price,discount,total,tax_object_id,created_at) ";
		$sql .= "value (".(int)$this->invoice_id.",".(int)$this->product_id.",\"$this->description\",$this->quantity,$this->price,$this->discount,$this->total,".(int)$this->tax_object_id.",$this->created_at)";
		return Executor::doit($sql);
	}

	public static function getAllByInvoiceId($id){
		$sql = "select * from ".self::$tablename." where invoice_id=$id";
		$query = Executor::doit($sql);
		return Model::many($query[0],new InvoiceItemData());
	}

	public function getProduct(){ return ProductData::getById($this->product_id); }
	public function getTaxObject(){ return TaxObjectData::getById($this->tax_object_id); }
}
?>
