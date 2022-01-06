<?php
require __DIR__.'/../sistem/PhpFunctionalLanguage.php';

/**
 * Se trae una ruta como si fuera un string, ese es el valor de retorno.
 */
if(! function_exists('response')) 
{
    function response($resourse) : string
    {
        return __DIR__.'/../resourses/'.$resourse;
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
 * Permite debuguear una variable en php.
 */
if(! function_exists('pre'))
{
    function pre($pre)
    {
        echo '<pre>';
        var_dump($pre);
        echo '</pre>';
    }
}



/**
 * Inicia la sesión de una persona.
 */
function sessionStarted()
{
    return iffn(
        fn()=>!isset($_SESSION), 
        function()
            {
                session_start();
                $_SESSION['session'] = true;
                return $_SESSION['session']; 
            },
        fn()=>false
    );
    /*
    if(!isset($_SESSION)){ session_start();}
    $_SESSION['session'] = true;*/
}

/**
 * Hace que las personas que accedan al recurso en cuestión deban tener la sesión iniciada.
 */
function activeSession(string $direction = '/')
{
    def($sessionStarted, run(
        function()
            {
                if(!isset($_SESSION)){ session_start();}
                return (!isset($_SESSION['session'])) 
                    ? false : $_SESSION['session'];
            }
        )
    );

    return iffn(
        fn()=>$sessionStarted === false,
        function() use ($direction)
        {
            header("Location:" . domain($direction));
            die();
        }
    );

    /*
    if(!isset($_SESSION)){ session_start();}
    $sessionStarted = (!isset($_SESSION['session'])) ? false : $_SESSION['session'] ;

    if ($sessionStarted == false) {
        header("Location:" . domain($direction));
        die();
    }
    */
}

/**
 * La ejecución de esta función cierra la sesión de una persona.
 */
function sessionEnded()
{
    return iffn(
        fn()=>!isset($_SESSION), 
        function()
            {
                session_start();
                $_SESSION['session'] = false;
                return $_SESSION['session']; 
            },
        fn()=>true
    );
    
    /*if(!isset($_SESSION)){ session_start();}
    $_SESSION['session'] = false;*/
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

/**
 * Permite imprimir el string de una función y elimina el 1 que sale si es que eso sale al final.
 */
function printFunction($printFunction)
{
    echo(rtrim($printFunction, '1'));
}

