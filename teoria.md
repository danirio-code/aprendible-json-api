# API -> Application Programming Interface

Son programas que permiten que otros programas se comuniquen con ellas a traves de peticiones (Request) y respuestas (Response)
El programa que se conecta a una API se denomina cliente. Y una API puede tener más de un cliente.

## API REST -> REpresentational State Transfer

#### 6 restricciones

1. Client-Server -> Separación de responsabilidades, la API debe estar separada del cliente
2. Stateless -> Cada petición contiene toda la información necesaria para que el servidor la procese. El estado de sesión del usuario se mantiene totalmente en el cliente. Ej: access tokens
3. Cacheable -> Las respuestas de la API se pueden almacenar en Caché de forma Implícita, Explícita y/o Negociable.
4. Uniform Interface -> La petición deberá identificar los recursos (información, datos, cualquier cosas que podamos nombrar) que está buscando (a través de una URL). La manipulación de recursos través de la representación. Un cliente REST necesita poco o ningún conocimiento previo sobre cómo interactuar con una API.
5. Layered System -> Los servicios REST están orientados a la escalabilidad y el cliente no sabe si la petición se realiza directamente a un servidor, un sistema de cachés, o por ejemplo un balanceador que se encarga de redirigirlo hacia un servidor final.
6. Code-on-demand -> El servidor puede extender o personalizar temporalmente la funcionalidad del cliente mediante la transferencia de lógica. Ej: Java Applets, JavaScript

Las API se consideran RESTful si además de cumplir con las 6 restricciones se comunican a través de HTTP (HyperText Transfer Protocol).

## Peticiones HTTP

1. Método (o verbo HTTP)
    - GET: obtener recursos
    - POST: crear un recurso
    - PUT: reemplazar un recurso
    - PATCH: actualizar un recurso
    - DELETE: eliminar un recurso
2. URI
    - GET: myjsonapi.com/articles/{id}
    - POST: myjsonapi.com/articles
    - PUT: myjsonapi.com/articles/{id}
    - PATCH: myjsonapi.com/articles/{id}
    - DELETE: myjsonapi.com/articles/{id}
3. Payload (cuerpo) -> Contiene información adicional

## Respuestas HTTP

-   1xx INFORMACIÓN
-   2xx EXITOSOS (200 OK, 201 Created, 204 No Content)
-   3xx REDIRECCIÓN (301 Moved Permanently, 302|303 Found at this other url, 307 Temporary redirect, 308 Permanent redirect)
-   4xx ERROR CLIENTE (400 Bad Rrequest, 401 Unauthorized, 403 Forbidden, 404 Not Found, 405 Method not allowed)
-   5xx ERROR SERVIDOR (500 Internal Server Error, 502 Bad Gateway, 503 Service Unavailable)

# JSON:API

Es una especificación de cómo un cliente debe solicitar que se busquen o modifiquen los recursos, y cómo un servidor debe responder a esas solicitudes.

## Ventajas

-   Convención
-   Herramientas para crear y consumir
-   Buenas prácticas

## Documento JSON:API

{
'data': [ contiene la información principal del documento, contiene: **type**, **id**, attributes{}, relationships{}, links{}, meta{}],
'errors'; [ contiene todos los objetos de error ],
'meta': { contiene cualquier información que se quiera añadir },
'included': [ contiene todos los objetos de recursos que están relacionados con los datos primarios ],
'links': { contiene los links de paginación },
'jsonapi': { contiene la versión de la api, si no se envía devuelve 1.0 },
}

## Párametros de la URL

-   Include -> indica que relación debe incluir en la respuesta (https://api.com/articles?include=authors,categories)
-   Sort -> sirve para ordenar los registros (https://api.com/articles?sort=-created-at,title)
-   Sparse Fieldsets -> sirve para definir que atributos queremos de uno o varios recursos (https://api.com/articles?fields[articles]=content)
-   Filter -> sirve para filtrar los resultados (https://api.com/articles?filter[title]=Laravel)
-   Page -> sirve para paginar los resultados (https://api.com/articles?page[size]=10&page[number]=2)

## Content Negotiation

Tanto las peticiones del Cliente como las respuestas del Servidor deben tener el header -> 'Content-Type': 'application/vnd.api+json'

---

Usar blueprint para generar la estructura de datos

1. composer require laravel-shift/blueprint –dev
2. php artisan blueprint:new -> generará un draft.yaml para crear la estructura
3. php artisan vendor:publish -> blueprint -> generará un /config/blueprint.php donde habrá que cambiar algunos datos
4. php artisan blueprint:build

---

Modificar cosas útilies para usar la api

-   En el routeServiceProvider se puede modificar el prefijo de todas las rutas del archivo api.php
-   Recursos para poner informacón extra. Por ejemplo la estructura que debe tener un artículo
-   Colecciones (recursos) para poner información extra y global del contenido, no solo el 'data'. Si se sigue la convención de nombres, automáticamente, la colección va a envolver cada elemento en el Resource del mismo nombre.
-
