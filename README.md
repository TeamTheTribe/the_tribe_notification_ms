# the_tribe_notification_ms
Module drupal microservice connection notifications (Version 0.1.0)

# Instalacion modulo
1. Importar el repositorio en el composer.json del proyecto
```
   repositories: [
      {
         "type": "vcs",
         "url": "git@github.com:TeamTheTribe/the_tribe_notification_ms.git"
      }
   ]
```
2. Instalar el módulo a través de Compose
```
   composer require the-tribe/the_tribe_notification_ms
```

# Autorización de servicio
1. Modificar inicio de sesión para incluir sharp id
- En el proceso de login añadir la linea
```
    \Drupal::request()->getSession()->set("sharp_id", SHARP_ID);
```
