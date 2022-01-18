<?php

/**
 * Obtiene la conexión a la base de datos y la asigna al simbolo $connection.
 */
def($connection, require __DIR__.'/connection.php');

/**
 * Permite capturar las funciones dentro de los controladores.
 */
def($request, function($controller, $method, $petition, $connect)
{
    return $GLOBALS[$controller]($method, $connect, $petition)();
});

/**
 * Permite capturar los modelos o las consultas sql.
 */
def($models, function($model, $method, $petition, $connect)
{
    return $GLOBALS[$model]($method, $connect, $petition);
});

/**
 * Es el sistema de rutas escrita de manera pura.
 */
def($routePure, function($route, $controller, $connect, $requestObject, $request_uri, $petition)
{
    def($routePath, function($controller)
    {
        def($controllerAndMethod, explode('@', $controller));
        return [
            "controllerRoute"   => $controllerAndMethod[0],
            "methodRoute"       => $controllerAndMethod[1]
        ];
    });

    def($request_uriFn, function($request_uri)
    {
        def($escapingGetExplode, explode('?', $request_uri));
        return (is_array($escapingGetExplode)) 
            ? $escapingGetExplode[0] 
            : $request_uri;
    });

    def($routeFn, fn($routeString)=> "/" . $routeString);

    def($requestRoute, function($routeRequest, $request_uriFn, $routePath, $requestObject) use ($connect, $petition)
    {
        return iffn(
            fn()=>  $routeRequest == $request_uriFn,
            fn()=>  $requestObject
                (
                    $routePath['controllerRoute'],
                    $routePath['methodRoute'],
                    $petition,
                    $connect
                )
        );
    });

    return $requestRoute
    (
        $routeFn($route), 
        $request_uriFn($request_uri), 
        $routePath($controller), 
        $requestObject
    );
});

/**
 * Permite ejecutar la función pura que utiliza la ruta, y por eso las variables del server así
 * como las peticines get y post se insertan en esta parte.
 */
def($routeFn, function($route, $controller) use ($connection, $request, $routePure)
{
    return $routePure($route, $controller, $connection, $request, $_SERVER['REQUEST_URI'] ?? null, $_REQUEST);
});

/**
 * Permite saber si se ha encontrado un recurso o no.
 */
def($resource_found, false);

/**
 * Permite definir las rutas que se usarán en el sistema.
 */
def($routePrint, function($route, $controller) use ($routeFn, &$resource_found)
{
    //def($routeWithoutPoint, explode('.', $route));
    def($resultRouteFn, $routeFn($route, $controller));
 
    printFunction(
        iffn(
            fn()=> null !== $resultRouteFn && $resource_found === false,
            function() use ($resultRouteFn, &$resource_found)
            {
                $resource_found = true;
                return $resultRouteFn;
            }
        )
    );
    //printFunction($view_or_resource_not_found);
    #var_dump($routeFn($route, $controller));
});

/**
 * Es lo que se ejecuta por defecto si el recurso no fue encontrado.
 */
def($resource_not_found, function($controller) use ($request, &$resource_found)
{
    //def($routeWithoutPoint, explode('.', $route));
    def($controllerMethod, explode('@', $controller));

    return iffn(
        fn()=> $resource_found === false,
        fn()=> $request($controllerMethod[0], $controllerMethod[1], null, null)
        //fn()=> 'Esto es un mensaje de error.'
    );
});

/**
 * Se trae una ruta como si fuera un string, ese es el valor de retorno.
 */
if(! function_exists('response')) 
{
    function response($resourse) : string
    {
        return __DIR__.'/../../resourses/'.$resourse;
        #return file_get_contents(__DIR__.'/../resourses/'.$resourse);
    };
}

/**
 * Se le pone una ruta y esta función se trae la ruta con un requiere, lo que 
 * hace que lo obtenido se imprima en el lugar en que se trajo.
 */
if(! function_exists('response_require'))
{
    function response_require($resourse_require) : string
    {
        return require response($resourse_require);
    };
}

/**
 * Retorna el dominio de la constante en el archivo env con una ruta agregada como argumento.
 */
if(! function_exists('domain')) 
{
    function domain($route) {
        return domain . '/' . $route;
    }
}


/**
 * Es una función que sirve para traerse una plantilla y ponerle algo dentro, si se pone el 
 * valor "data" entonces se puede enviar elementos a la plantilla. Así si se pone solo un 
 * string, entonces ese estring para que se vea dentro de la plantilla, solo se ejecuta la 
 * función contend, donde se va a ver lo que se le envía, pero si la plantilla tiene elementos 
 * donde se introducen varias cosasentonces lo que se hace es enviar en el contend_insert un 
 * array, y en la plantilla, se ejecuta la función y como va a retornar el array, entonces 
 * se pone el índice del array y se ejecuta esa función, y todo eso se hace dentro de la 
 * función printFunction();
 */
function template($template_require, $contend_insert)
{
    def($contend, iffn(fn()=>is_array($contend_insert), 
        fn()=>  fn()=>  $contend_insert,
        fn()=>  fn()=>  require response($contend_insert)
    )); 
    return require response($template_require);
}

