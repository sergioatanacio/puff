<?php
/**
 * Obtiene la conección a la base de datos y la asigna al simbolo $connection.
 */
def($connection, require __DIR__.'/../sistem/connection.php');

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
 * Permite definir las rutas que se usarán en el sistema.
 */
def($routePrint, function($route, $controller) use ($routeFn)
{
    def($routeWithoutPoint, explode('.', $route));

    printFunction($routeFn($routeWithoutPoint[0], $controller));
    #var_dump($routeFn($route, $controller));
});


