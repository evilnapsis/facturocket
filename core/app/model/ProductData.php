<?php
class ProductData {
	public static $tablename = "product";

	public $id, $name, $code, $description, $price, $category_id, $product_type_id, $unit_id, $tax_id, $created_at;

	public function __construct(){
		$this->name = "";
		$this->code = "";
		$this->description = "";
		$this->price = 0;
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (name,code,description,price,category_id,product_type_id,unit_id,tax_id,created_at) ";
		$sql .= "value (\"$this->name\",\"$this->code\",\"$this->description\",$this->price,$this->category_id,$this->product_type_id,$this->unit_id,$this->tax_id,$this->created_at)";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",code=\"$this->code\",description=\"$this->description\",price=$this->price,category_id=$this->category_id,product_type_id=$this->product_type_id,unit_id=$this->unit_id,tax_id=$this->tax_id where id=$this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ProductData());
	}

	
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		return Executor::doit($sql);
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public function getCategory(){ return CategoryData::getById($this->category_id); }
	public function getProductType(){ return ProductTypeData::getById($this->product_type_id); }
	public function getUnit(){ return UnitData::getById($this->unit_id); }
	public function getTax(){ return TaxData::getById($this->tax_id); }
}
?>

