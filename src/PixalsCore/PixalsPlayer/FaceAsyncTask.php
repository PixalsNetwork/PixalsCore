<?php


namespace PixalsCore\PixalsPlayer;

use PixalsCore\Pixals;
use pocketmine\scheduler\AsyncTask;

class FaceAsyncTask extends AsyncTask {

    private $png;
    private $file_name;
    
    public function __construct(String $png, String $file_name)
    {
        $this->file_name = $file_name;
        $this->png = $png;
    }

    public function onRun(): void
    {
        $url = "https://api.cloudflare.com/client/v4/accounts/954abbeee840445fd27b3081d9ea3ff3/r2/buckets/pixalsapi/objects/player_faces/{$this->file_name}";

   
        $headers = [
            "Authorization: Bearer 1lk3PAJIUSQUoyCnCt4IJEdziefvcYmAwLT-Se6f",
            "Content-Type: image/png",
            "Content-Length: " . strlen($this->png),
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $this->png,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($response === false){
            $this->setResult("curl_error: " . curl_error($ch));
            curl_close($ch);
            return;
        }

        curl_close($ch);

        // Log the real HTTP status code from Cloudflare
        $this->setResult([
            "http_status" => $status,
            "url" => $url,
            "response" => $response,
        ]);
    }

    public function onCompletion(): void
    {
        $result = $this->getResult();
        $logger = Pixals::getPluginInstance()->getLogger();

        if(is_array($result)){
            if($result['http_status'] === 200 || $result['http_status'] === 201){
                $logger->info("§7Face uploaded successfully.");
            } else {
                $logger->error("§7Face upload failed: HTTP " . $result['http_status'] . " | URL: " . $result['url']);
                $logger->error("§7Response: " . $result['response']);
            }
        }else{
            // curl error
            $logger->error("§7" . $result);
        }
    }

}