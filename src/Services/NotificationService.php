<?php

namespace Drupal\the_tribe_notification_ms\Services;

use Drupal\user\Entity\User;
use Exception;
use Throwable;

final class NotificationService
{
    private $appURL;
    private $notificationURL;
    private $endPoint;
    protected $currentUser; 


    public function __construct()
    {
        $this->appURL = \Drupal::request()->getSchemeAndHttpHost();
        $this->notificationURL =  getenv('MS_NOTIFICATION_URL');
        $this->endPoint = $this->notificationURL.'/api/notifications';
        $this->currentUser = \Drupal::currentUser();
    }

    public function getNotifications()
    {
        if(!$this->authorize()){
            throw new Exception('Not authorized by user ID permissions');
        }

        try {
            
            $headers = [
                'Content-Type: application/json',
            ];

            return $this->client('GET', $this->endPoint."/".$this->getIdentityUser(), null, $headers);            

        } catch (Throwable $th) {
            return [
                'try' => true,
                'message' => $th->getMessage()
            ];
        }
        
    }

    public function save(array $content, 
                        array $params, 
                        string $action, 
                        int $categoryId, 
                        ?string $groupId = NULL)
    {
        try {
            $headers = [
                'Content-Type: application/json',
                'Referer: '. str_replace(['http://', 'https://'], '',  $this->appURL),
                'Host: '. str_replace(['http://', 'https://'], '', $this->appURL)
            ];
    
            $body = [
                'content' => json_encode($content),
                'params' => json_encode($params),
                'action' => $action,
                'category_id' => $categoryId,
                'group_id' => $groupId
            ];
    
            return $this->client('PUT', $this->endPoint, json_encode($body), $headers);

        
        } catch (Throwable $th) {
            return [
                'try' => true,
                'message' => $th->getMessage()
            ];
        }
    }

    public function readNotification(string $notificationId)
    {
        if(!$this->authorize()){
            throw new Exception('Not authorized by user ID permissions');
        }

        try {
            $headers = [
                'Content-Type: application/json',
            ];
            
            $body = [
                "sharp_id" => $this->getIdentityUser()
            ];
    
            
            return $this->client('PUT', $this->endPoint."/".$notificationId, json_encode($body), $headers);

        } catch (Throwable $th) {
            return [
                'try' => true,
                'message' => $th->getMessage()
            ];
        }
        
    }

    public function deleteNotification(string $notificationId)
    {

        if(!$this->authorize()){
            throw new Exception('Not authorized by user ID permissions');
        }

        
        try {
            $headers = [
                'Content-Type: application/json',
            ];
    
            $body = [
                "sharp_id" => $this->getIdentityUser()
            ];

            return $this->client('DELETE', $this->endPoint."/".$notificationId, json_encode($body), $headers);

        } catch (Throwable $th) {
            return [
                'try' => true,
                'message' => $th->getMessage()
            ];
        }
        
    }

    public function getResource()
    {
        $file = $this->notificationURL .'/js/notifications.js';
        
        try{
            header('Contet-Type: text/javascript');
            header('Content-type: text/js;');
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($file);
            exit;
        }catch(Throwable $th){
            echo $th->getMessage();
            exit;
            return $th;
        }
    }

    private function authorize()
    {
        return true;
    }

    private function getIdentityUser()
    {
        return $this->currentUser->id();
        /*
        Identifier parameter definition

        $user = User::load($this->currentUser->id());
        $sharp_id = $user->get('field_sharp_id')->value;
        if (!$sharp_id) {
            throw new \Exception("There is no session variable sharp_id");
        }
        return $sharp_id;
        */
    }

    private function client(string $method, string $url, ?string $body = null, ?array $headers = null){

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER =>  $headers,
        ));

        
        if(!is_null($headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        if(!is_null($body)){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }


        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if(!$response){
            throw new Exception(curl_error($curl));
        }

        return [
            'body' => $response,
            'code' => $httpCode
        ];
    }
    

}
