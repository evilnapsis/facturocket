<?php
class PaymentData {
	public static $tablename = "payment";
    public $id, $invoice_id, $payment_form_id, $amount, $saldo_anterior, $saldo_insoluto, $num_parcialidad, $date_at, $uuid, $xml_path, $observations, $is_stamped, $created_at;

	public function __construct(){
		$this->id = "";
		$this->invoice_id = "";
		$this->payment_form_id = "";
		$this->amount = "";
		$this->saldo_anterior = "";
		$this->saldo_insoluto = "";
		$this->num_parcialidad = "";
		$this->date_at = "";
		$this->uuid = "";
		$this->xml_path = "";
		$this->observations = "";
		$this->is_stamped = 0;
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (invoice_id, payment_form_id, amount, saldo_anterior, saldo_insoluto, num_parcialidad, date_at, observations, created_at) ";
		$sql .= "value (\"$this->invoice_id\",\"$this->payment_form_id\",\"$this->amount\",\"$this->saldo_anterior\",\"$this->saldo_insoluto\",\"$this->num_parcialidad\",\"$this->date_at\",\"$this->observations\",$this->created_at)";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentData());
	}

	public static function getAllByInvoiceId($id){
		$sql = "select * from ".self::$tablename." where invoice_id=$id order by created_at asc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentData());
	}

	public function getPaymentForm(){ return PaymentFormData::getById($this->payment_form_id); }
}
?>
