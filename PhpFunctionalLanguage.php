<?php

/**
 * Permite crear una variable a la que no puede cambiarse el valor al estilo de los 
 * simbolos en el paradigma funcional.
 */
function def(&$symbol, $value)
{
    if(isset($symbol))
    {
        die('No se puede cambiar el valor "' . $symbol().'". El programa se ha detenido en este punto.');
    } else
    {
        $symbol = $value;
    }
}

/**
 * Permite usar un if ternario de manera funciónal, y el resultado se va a retornar para poder 
 * ser asignado a una variable.
 */
function iffn($condition, $true, $false = null)
{
    if($condition())
    {
        return $true();
    }
    elseif($false !== null)
    {
        return $false();
    }
    else
    {
        return null;
    }
}

/**
 * Permite insertar código no funcional para no tener que hacer toda una cópia de cada elemento
 * y para no tener que cambiar el valor de una variable de manera no funcional en el scope global.
 */
function run($data, $run = null)
{
    return iffn(
        fn()=> $run === null,
        fn()=> $data(),
        fn()=> $run($data)
    ); ;
    #return $run($data);
}
/*
Este es un ejemplo de uso
$persona = ['nombre' => null, 'nombre_dos' => null];
$persona2 = run($persona, function($dato)
{
   $dato['nombre'] = true;
   return $dato;
});
var_dump($persona); var_dump($persona2);
*/


/**
 * Permite cambiar el valor de una variable eligiendolo por el indice.
 */
function editIndex($data, $index, $insert)
{
    $data[$index] = $insert;
    return $data;
}
/*
$persona = [ 
    'datos_personales'    => [
            'nombre'    => 'julio',
            'apellido'  => 'quispe'
        ]
];
$persona2 = editIndex($persona, 'datos_personales',
    editIndex($persona['datos_personales'], 'nombre', ['mauro', 'bernardo'])
);
var_dump($persona); var_dump($persona2);
*/


/**
 * Recibe un array y retorna su último elemento.
 */
if(! function_exists('lastElement')) 
{
    function lastElement($lastElement) 
    {
        return iffn(
            fn()=>!is_array($lastElement) || $lastElement == [],
            fn()=>[],
            fn()=>$lastElement[count($lastElement) - 1]
        );
    }
}

/**
 * Recive un array y lo devuelve eliminado su último elemento.
 */
if(! function_exists('popArray')) 
{
    function popArray($popFunction)
    {
        array_pop($popFunction);
        return $popFunction;
    }
}

/**
 * Permite realizar una operación pero de manera funcional y recursiva. El primer 
 * valor es un string con el valor de un operador. Los siguientes valores son los elementos que
 * se usarán para aplicar la operación. Si solo se colocan 2 argumentos, entonces el primer 
 * argumento se usa como operador, y el segundo debe ser un array con los datos con los cuales
 * se van a realizar la operación.
 */
/*
function operation(string $operator, array $values, $result = null)
{
    if($values === [])
    {
        return $result;
    }
    else
    {
        if ($result === null) 
        {
            $values = array_reverse($values);
            $result = lastElement($values);
            $values = popArray($values);
        }
            $sendEvalResult = eval('return '.$result.' '.$operator.' '.lastElement($values).';');
            return operation($operator, popArray($values), $sendEvalResult);
    }
}*/
function operation()
{
    def($args, array_reverse(func_get_args()));

    def($operation, 
        function(string $operator, array $values, $result = null) use (&$operation)
        {
            def($firstValue, iffn(
                fn()=>$result === null,
                fn()=>lastElement($values),
                fn()=>$result
            ));
            
            def($secondValue, iffn(
                fn()=>$result === null,
                fn()=>lastElement(popArray($values)),
                fn()=>lastElement($values)
            ));
            
            def($processedValues, iffn(
                fn()=>$result === null,
                fn()=>popArray(popArray($values)),
                fn()=>popArray($values)
            ));
            
            return iffn(
                fn()=>$values === [],
                fn()=>$result,
                function() use ($operator, $processedValues, $firstValue, $secondValue, $operation)
                {
                    def($sendEvalResult, eval('return '.$firstValue.' '.$operator.' '.$secondValue.';'));
                    return $operation($operator, $processedValues, $sendEvalResult); 
                }
            );
        }
    );

    return iffn(
        fn()=>count($args) !== 2,
        fn()=>$operation(lastElement($args), popArray($args)),
        fn()=>$operation(lastElement($args), array_reverse($args[0]))
    );

    #return $operation(lastElement($args), popArray($args));
}


/**
 * Convierte el resultado de una consulta SQL en un array.
 */
if(! function_exists('assocQuery'))
{
    function assocQuery($query, $index = null)
    {   
        return iffn(
            fn()=>$query != null,
            fn()=>run($query, function($petition) use ($index)
            {
                $result = [];
                while ($elements = $petition->fetch(\PDO::FETCH_ASSOC))
                {
                    $result[] = ($index != null) ? $elements[$index] : $elements;
                }
                return $result;
            }),
            fn()=>null,
        );
        /*
        if ($query != null) {
            $result = [];
            while ($elements = $query->fetch(\PDO::FETCH_ASSOC))
            {
                $result[] = ($index != null) ? $elements[$index] : $elements;
            }
        } else {
            $result = null;
        }
        
        return $result;
        */
    }
}

/**
 * Agrega un elemento al ultimo lugar de un array. O tambien puede decirse que fuciona dos elementos 
 * en un array.
 */
if(! function_exists('joinArrangement'))
{
    function joinArrangement($array, $newItem = null) : array
    {        
        def($fixReturned, 
            iffn(fn()=>is_array($array),
                fn()=>$array,
                iffn(fn()=>$array == null || $array == '',
                    fn()=>[],
                    fn()=>[$array]
                )
            )
        );

        return iffn(
            fn()=>isset($newItem) && $newItem != [],
            fn()=>run($fixReturned, function($arrayNewItem) use ($newItem)
                {
                    $arrayNewItem[] = $newItem;
                    return $arrayNewItem;
                }),
            fn()=>$fixReturned
        );
    }
}

/**
 * En un array de varios elementos, o un array multinivel, permite seleccionar elementos del array
 * por el indice, por ejemplo en un array multinivel de personas donde se encuentran datos como
 * el nombre, la edad el sexo, etc, permite obtener un array con todos los nombres de las 
 * personas que se encuentran en ese array.
 */
if(! function_exists('getAnItem'))
{
    function getAnItem(array $array, string $index = null, array $result = [])
    {
        return iffn(
            fn()=>!is_array($array) || $array == [],
            fn()=>$result,
            function() use ($array, $index, $result)
            {
                def($lastElement, lastElement($array));
                def($resultingElement, $lastElement[$index]);
                return getAnItem(popArray($array), $index, joinArrangement($result, $resultingElement));
            },
        );

        /*
        if (!is_array($array) || $array == []) {
            return $result;
        } else {
            $lastElement = lastElement($array);
            $resultingElement = $lastElement[$index];
            return getAnItem(popArray($array), $index, joinArrangement($result, $resultingElement));
        }
        */
    }
}
