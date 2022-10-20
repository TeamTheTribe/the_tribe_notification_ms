<?php

namespace Drupal\the_tribe_notification_ms\Services;

use Exception;
use Throwable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

final class NotificationService
{
    private $client;
    private $appURL;
    private $notificationURL;
    private $endPoint;
    protected $currentUser; 


    public function __construct()
    {
        $this->appURL = \Drupal::request()->getSchemeAndHttpHost();
        $this->notificationURL =  getenv('MS_NOTIFICATION_URL');
        $this->endPoint = $this->notificationURL.'/api/notifications';
        $this->client = new Client();
        $this->currentUser = \Drupal::currentUser();
    }

    public function getNotifications(int $page = 1)
    {
        if(!$this->authorize()){
            throw new Exception('Not authorized by user ID permissions');
        }

        try {
            $headers = [
                'Content-Type' => 'application/json'
            ];
            
            

            return $this->client->request('GET', "$this->endPoint/{$this->getIdentityUser()}?page=$page", [
                'headers' => $headers
            ]);
        } catch (BadResponseException $th) {
            return $th->getResponse();
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
    
            return $this->client->request('PUT', $this->endPoint, [
                'headers' => $headers,
                'body' => json_encode($body)
            ]);

        
        } catch (BadResponseException $th) {
            return $th->getResponse();
        }
    }

    public function readNotification(string $notificationId)
    {
        if(!$this->authorize()){
            throw new Exception('Not authorized by user ID permissions');
        }

        try {
            $headers = [
                'Content-Type' => 'application/json'
            ];
    
            return $this->client->request('PUT', $this->endPoint."/".$notificationId, [
                'headers' => $headers,
                'form_params' => ["sharp_id" => $this->getIdentityUser()]
            ]);
        } catch (BadResponseException $th) {
            return $th->getResponse();
        };
    }

    public function deleteNotification(string $notificationId)
    {

        if(!$this->authorize()){
            throw new Exception('Not authorized by user ID permissions');
        }
        
        try {
            $headers = [
                'Content-Type' => 'application/json'
            ];
    
            return $this->client->request("DELETE", $this->endPoint."/".$notificationId, [
                'headers' => $headers,
                'form_params' => ["sharp_id" => $this->getIdentityUser()]
            ]);
        } catch (BadResponseException $th) {
            return $th->getResponse();
        }
        
    }

    public function getResource()
    {
        $file = $this->notificationURL .'/js/notifications.js';

        try{
            header('Content-Type: text/javascript');
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($file);
            exit;
        }catch(Throwable $th){
            return $th;
        }
    }

    private function authorize()
    {
        return true;
    }

    private function getIdentityUser()
    {
        $sharp = \Drupal::request()->getSession()->get('sharp_id', null);
        if(is_null($sharp)){
            throw new Exception("Session does not have sharp_id value");
        }
        return $sharp;
    }
}
