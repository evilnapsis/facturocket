<?php
if(isset($_SESSION["user_id"]) && isset($_GET["opt"]) && ($_GET["opt"]=="upd" || $_GET["opt"]=="upd_ajax")){
    
    foreach($_POST as $key => $val){
        if(strpos($key, "serie_") !== false){
            $type = str_replace("serie_", "", $key);
            $sql = "update folio_sequence set serie=\"$val\" where type_code=\"$type\"";
            Executor::doit($sql);
        }
        else if(strpos($key, "folio_") !== false){
            $type = str_replace("folio_", "", $key);
            $sql = "update folio_sequence set next_folio=$val where type_code=\"$type\"";
            Executor::doit($sql);
        }
        else if($key != "csd_pass"){
             SettingData::updateValByShort($key, $val);
        }else{
             SettingData::updateValByShort($key, $val);
        }
    }

    // Handle File Uploads
    $storage_path = "storage/branding/";
    if(!file_exists($storage_path)){ mkdir($storage_path, 0777, true); }

    $files = ["logo", "csd_cert", "csd_key"];
    foreach($files as $file_key){
        if(isset($_FILES[$file_key]) && $_FILES[$file_key]["name"] != ""){
            $name = time()."_".$_FILES[$file_key]["name"];
            if(move_uploaded_file($_FILES[$file_key]["tmp_name"], $storage_path.$name)){
                SettingData::updateValByShort($file_key, $name);
            }
        }
    }

    if($_GET["opt"]=="upd_ajax"){
        echo json_encode(["status" => "success", "message" => "Configuración guardada correctamente."]);
        exit;
    }

    $_SESSION["sweetalert"] = "Configuración actualizada correctamente.";
    $_SESSION["sweetalert_icon"] = "success";
    Core::redir("./?view=settings");
}
?>
