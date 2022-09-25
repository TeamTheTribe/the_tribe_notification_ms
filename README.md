# the_tribe_notification_ms
Module drupal microservice connection notifications (Version 0.1.0)

# Instalacion modulo
1. Descargar repositorio o Zip eh incluir en el proyecto
```
   project/modules/custom/the_tribe_notification_ms
```

# Parametrizar uso de servicio si el usuario contiene identificador establecido (getIdentityUser)
1. Editar o sobreescribir metodo (authorize())
```
    private function authorize()
    {
        return true;
    }
```

# Definir identificador notificaciones por usuario
1. Editar o sobreescribir metodo (getIdentityUser())
```
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
```
