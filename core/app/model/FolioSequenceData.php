<?php
class FolioSequenceData {
	public static $tablename = "folio_sequence";
    public $id, $type_code, $serie, $next_folio;

	public function __construct(){
		$this->id = "";
		$this->type_code = "";
		$this->serie = "";
		$this->next_folio = "";
	}

	public static function getByCode($code){
		$sql = "select * from ".self::$tablename." where type_code=\"$code\"";
		$query = Executor::doit($sql);
		return Model::one($query[0],new FolioSequenceData());
	}

	public static function getNext($code){
		$seq = self::getByCode($code);
		$next = $seq->next_folio;
		$serie = $seq->serie;

		// Incrementamos para la próxima vez
		$new_folio = $next + 1;
		$sql = "update ".self::$tablename." set next_folio=$new_folio where type_code=\"$code\"";
		Executor::doit($sql);

		return ["serie"=>$serie, "folio"=>$next];
	}

    public static function getAll(){
        $sql = "select * from ".self::$tablename;
        $query = Executor::doit($sql);
        return Model::many($query[0],new FolioSequenceData());
    }
}
?>
