<?php

class FacturamaEngine {

    public static function stamp($xml_content) {
        $user = SettingData::getByShort("facturama_user")->val;
        $pass = SettingData::getByShort("facturama_pass")->val;
        
        $url = "https://apisandbox.facturama.mx/api-v3/cfdi/stamp";
        
        // Inyectamos un RFC de prueba universal para asegurar que el Sandbox lo acepte
        $xml_content = preg_replace('/Rfc="[A-Z0-9]{12,13}"/', 'Rfc="EKU9003173C9"', $xml_content, 1);
        
        $data = [
            "XmlContent" => base64_encode($xml_content)
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        // Autenticación Nativa
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        file_put_contents("storage/facturama_debug.txt", "HTTP: $http_code | Error: $curl_error | Response: " . $response);

        if ($curl_error) {
             return ["success" => false, "message" => "Error de conexión (cURL): " . $curl_error];
        }

        $result = json_decode($response, true);

        if (($http_code == 201 || $http_code == 200) && !empty($result)) {
            $xml_base64 = isset($result["Content"]) ? $result["Content"] : (isset($result["XmlContent"]) ? $result["XmlContent"] : "");
            $uuid = isset($result["Uuid"]) ? $result["Uuid"] : "";

            if ($xml_base64 != "") {
                return [
                    "success" => true,
                    "xml" => base64_decode($xml_base64),
                    "uuid" => $uuid
                ];
            }
        }
        
        return [
            "success" => false,
            "message" => "Facturama respondió con éxito (200) pero sin datos. Intenta registrar tu RFC en el panel o usa Finkok.",
            "details" => $result
        ];
    }
}
?>
