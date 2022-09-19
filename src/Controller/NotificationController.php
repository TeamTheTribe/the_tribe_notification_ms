<?php


namespace Drupal\the_tribe_notification_ms\Controller;

use Drupal\the_tribe_notification_ms\Services\NotificationService;
use Drupal\Core\Controller\ControllerBase;
use Exception;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class  NotificationController extends ControllerBase{
    
    protected $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function getNotifications()
    {
        $response = $this->notificationService->getNotifications();
        if(isset($response['try']))
        {
            throw new Exception($response['message']);
        }
        return new JsonResponse(json_decode($response['body'], true), $response['code']);
    }

    public function save(Request $request)
    {
        $response = $this->notificationService->save(
            $request->get("content"), 
            $request->get("params"),
            $request->get("action"),
            $request->get("category_id"),
            $request->get("group_id")
        );
        if(isset($response['try']))
        {
            throw new Exception($response['message']);
        }
        return new JsonResponse(json_decode($response['body'], true), $response['code']);
    }

    public function readNotification($notificationId)
    {
        $response = $this->notificationService->readNotification($notificationId);
        if(isset($response['try']))
        {
            throw new Exception($response['message']);
        }
        return new JsonResponse(json_decode($response['body'], true), $response['code']);
    }

    public function deleteNotification($notificationId)
    {
        $response = $this->notificationService->deleteNotification($notificationId);
        if(isset($response['try']))
        {
            throw new Exception($response['message']);
        }
        return new JsonResponse(json_decode($response['body'], true), $response['code']);
    }

    public function getResourceNotification(){
        $response = $this->notificationService->getResource();
        if( $response instanceof \Throwable){
            $errors = [
                'method'    => __METHOD__,
                'class'     => $response->getFile(),
                'line'      => $response->getLine(),
                'message'   => $response->getMessage(),
                'trace'     => $response->getTraceAsString(),
            ];

            \Drupal::logger('Getting notification.js resource')->notice(json_encode($errors));

            $response = '';
        }

        return new Response($response);
    }
}