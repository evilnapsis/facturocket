<?php
class InvoiceData {
	public static $tablename = "invoice";

	public $id, $serie, $folio, $date, $client_id, $cfdi_use_id, $payment_form_id, $payment_method_id, $currency, $exchange_rate, $subtotal, $discount, $total, $type, $status, $uuid, $xml_path, $pdf_path, $user_id, $created_at;

	public function __construct(){
		$this->serie = "";
		$this->folio = "";
		$this->date = "NOW()";
		$this->currency = "MXN";
		$this->exchange_rate = 1;
		$this->subtotal = 0;
		$this->discount = 0;
		$this->total = 0;
		$this->type = "I";
		$this->status = 1;
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (serie,folio,date,client_id,cfdi_use_id,payment_form_id,payment_method_id,currency,exchange_rate,subtotal,discount,total,type,status,uuid,xml_path,pdf_path,user_id,created_at) ";
		$sql .= "value (\"$this->serie\",\"$this->folio\",$this->date,".(int)$this->client_id.",".(int)$this->cfdi_use_id.",".(int)$this->payment_form_id.",".(int)$this->payment_method_id.",\"$this->currency\",$this->exchange_rate,$this->subtotal,$this->discount,$this->total,\"$this->type\",$this->status,\"$this->uuid\",\"$this->xml_path\",\"$this->pdf_path\",".(int)$this->user_id.",$this->created_at)";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new InvoiceData());
	}

	
	public function del(){
		$sql = "delete from ".self::$tablename." where id=$this->id";
		return Executor::doit($sql);
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new InvoiceData());
	}

	public function getClient(){ return ClientData::getById($this->client_id); }
	public function getCFDIUse(){ return CFDIUseData::getById($this->cfdi_use_id); }
	public function getPaymentForm(){ return PaymentFormData::getById($this->payment_form_id); }
	public function getPaymentMethod(){ return PaymentMethodData::getById($this->payment_method_id); }
	public function getUser(){ return UserData::getById($this->user_id); }

    public function update_pdf(){
        $sql = "update ".self::$tablename." set pdf_path=\"$this->pdf_path\" where id=$this->id";
        return Executor::doit($sql);
    }

    public function update_xml(){
        $sql = "update ".self::$tablename." set xml_path=\"$this->xml_path\", status=$this->status, uuid=\"$this->uuid\" where id=$this->id";
        return Executor::doit($sql);
    }
}
?>

