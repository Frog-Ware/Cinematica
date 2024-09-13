<?php

// Este script devuelve los datos requeridos por diferentes scripts.

require_once "conn.php";
require_once "../utilities/validacion.php";

// Funciones de Inicio de Sesión

// Devuelve la contraseña asociada al usuario poseedor del email ingresado.
function traerPasswd($email)
{
    $consultaSql = "SELECT passwd FROM Usuario WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    return (isset($datos['passwd']) && !is_null($datos)) ?
        $datos['passwd'] : null;
}

// Devuelve los datos asociados al usuario poseedor del email ingresado.
function traerUsuario($email)
{
    $consultaSql = "SELECT email, nombre, apellido, imagenPerfil, numeroCelular FROM Usuario WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve el rol del usuario en cuestión.
function traerRol($email)
{
    $consultaSql = "SELECT email, 1 AS rol FROM Empleado WHERE email = ? UNION ALL SELECT email, 0 AS rol FROM Cliente WHERE email = ?";
    $rol = consultaUnica($consultaSql, [$email, $email])['rol'];
    if (is_null($rol)) {
        return null;
    } else if ($rol) {
        $consultaSql = "SELECT esAdmin FROM Empleado WHERE email = ?";
        $rol = consultaUnica($consultaSql, [$email])['esAdmin'] ? 2 : 1;
    }
    return $rol;
}

// Devuelve el token de cambio de contraseña del usuario en cuestión.
function traerToken($email)
{
    $consultaSql = "SELECT token FROM Usuario WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    return (!is_null($datos) && isset($datos['token'])) ?
        $datos['token'] : null;
}

// Devuelve una lista de los clientes con cuenta.
function traerClientes()
{
    //$consultaSql = "SELECT email FROM Cliente";
    $consultaSql = "SELECT idCompra FROM Compra";
    foreach (consulta($consultaSql) as $x)
        $ids[] = $x['email'];
    if (is_null($ids))
        return null;
    foreach ($ids as $x)
        $datos[] = traerPelicula($x);
    return $datos;
}



// Funciones de datos referidos a los productos

// Devuelve la pelicula asociada al ID ingresado.
function traerPelicula($id)
{
    $consultaSql = "SELECT * FROM Pelicula WHERE idProducto = ?";
    $datos = consultaUnica($consultaSql, [$id]);
    if (is_null($datos))
        return null;
    $consultas = [
        'nombreCategoria' => "SELECT nombreCategoria FROM tieneCategorias WHERE idProducto = ?",
        'dimension' => "SELECT dimension FROM tieneDimensiones WHERE idProducto = ?",
        'idioma' => "SELECT idioma FROM tieneIdiomas WHERE idProducto = ?"
    ];
    foreach ($consultas as $k => $v)
        $datos[$k] = array_column(consultaClave($v, [$id]), $k);
    return $datos;
}

// Devuelve la pelicula según su nombre.
function traerPeliculaNombre($n)
{
    $consultaSql = "SELECT * FROM Pelicula WHERE nombrePelicula = ?";
    $datos = consultaUnica($consultaSql, [$n]);
    if (is_null($datos))
        return null;
    $consultas = [
        'nombreCategoria' => "SELECT nombreCategoria FROM tieneCategorias WHERE idProducto = ?",
        'dimension' => "SELECT dimension FROM tieneDimensiones WHERE idProducto = ?",
        'idioma' => "SELECT idioma FROM tieneIdiomas WHERE idProducto = ?"
    ];
    foreach ($consultas as $k => $v)
        $datos[$k] = array_column(consultaClave($v, [$datos['idProducto']]), $k);
    return $datos;
}

// Devuelve todas las peliculas.
function traerPeliculas()
{
    $consultaSql = "SELECT idProducto FROM Pelicula";
    foreach (consulta($consultaSql) as $x)
        $ids[] = $x['idProducto'];
    if (is_null($ids))
        return null;
    foreach ($ids as $x)
        $datos[] = traerPelicula($x);
    return $datos;
}

// Devuelve la cartelera de películas en su totalidad.
function traerCartelera()
{
    $consultaSql = "SELECT * FROM Cartelera";
    $ids = consulta($consultaSql);
    if (is_null($ids))
        return null;
    foreach ($ids as $x)
        $datos[] = traerPelicula($x['idProducto']);
    return $datos;
}

function traerIdCartelera()
{
    $consultaSql = "SELECT * FROM Cartelera";
    $datos = consulta($consultaSql);
    return !is_null($datos) ?
        $datos : null;
}

// Devuelve las categorías disponibles.
function traerCategorias()
{
    $consultaSql = "SELECT * FROM Categorias";
    $datos = consulta($consultaSql);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve las dimensiones disponibles.
function traerDimensiones()
{
    $consultaSql = "SELECT dimension FROM Dimensiones";
    $datos = consulta($consultaSql);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve los idiomas disponibles.
function traerIdiomas()
{
    $consultaSql = "SELECT * FROM Idiomas";
    $datos = consulta($consultaSql);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve el artículo asociada al ID ingresado.
function traerArticulo($idProducto)
{
    $consultaSql = "SELECT '*' FROM Articulo WHERE idProducto = ?";
    $datos = consultaUnica($consultaSql, [$idProducto]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve el artículo asociada al nombre ingresado.
function traerArticuloNombre($n)
{
    $consultaSql = "SELECT '*' FROM Articulo WHERE nombreArticulo = ?";
    $datos = consultaUnica($consultaSql, [$n]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve la lista de artículos en su totalidad. 
function traerArticulos()
{
    $consultaSql = "SELECT * FROM Articulo";
    $datos = consulta($consultaSql);
    return (!is_null($datos)) ?
        $datos : null;
}

function traerBusqueda($busqueda)
{
    $consultaSql = "SELECT idProducto FROM Pelicula WHERE nombrePelicula LIKE ?";
    $id = consultaClave($consultaSql, [$busqueda]);
    if (is_null($id))
        return null;
    foreach ($id as $x)
        $datos[] = array_merge(traerPelicula($x['idProducto']), $x);
    return $datos;
}

// Trae los registros de compra asociados a ese producto.
function traerRegistro($idProducto)
{
    $consultaSql = "SELECT * FROM Compra WHERE idProducto = ?";
    $datos = consultaClave($consultaSql, [$idProducto]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve los datos del carrito.
function traerCarrito($email)
{
    $consultaSql = "SELECT * FROM Carrito WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    if (is_null($datos))
        return null;
    $consultaSql = "SELECT idProducto, cantidad FROM CarritoArticulo WHERE email = ?";
    $datos['articulos'] = consultaClave($consultaSql, [$email]);
    return $datos;
}

function traerFunc($idFuncion)
{
    $consultaSql = "SELECT * FROM Funciones WHERE idFuncion = ?";
    $datos = consultaClave($consultaSql, [$idFuncion]);
    return (!is_null($datos)) ?
        $datos : null;
}

function traerFuncFecha($fecha)
{
    $consultaSql = "SELECT * FROM Funciones WHERE fechaPelicula = ?";
    $datos = consultaClave($consultaSql, [$fecha]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve las funciones programadas desde el día actual (incluido) en adelante.
function traerFuncFuturas()
{
    $fecha = new DateTime('now', new DateTimeZone('America/Montevideo'));
    $consultaSql = "SELECT * FROM Funciones WHERE fechaPelicula >= ?";
    $datos = consultaClave($consultaSql, [$fecha->format('Y-m-d')]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve las funciones programadas para una pelicula en específico desde el día actual (incluido) en adelante.
function traerFuncFuturasEsp($idProducto)
{
    $fecha = new DateTime('now', new DateTimeZone('America/Montevideo'));
    $consultaSql = "SELECT * FROM Funciones WHERE idProducto = ? AND fechaPelicula >= ?";
    $datos = consultaClave($consultaSql, [$idProducto, $fecha->format('Y-m-d')]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve los asientos ocupados en una funcion.
function traerAsientos($idFuncion)
{
    $consultaSql = "SELECT asientosOcupados FROM Funciones WHERE idFuncion = ?";
    $datos = consultaUnica($consultaSql, [$idFuncion]);
    return (!is_null($datos)) ?
        $datos['asientosOcupados'] : null;
}

function traerCines()
{
    $consultaSql = "SELECT * FROM Cine";
    $datos = consulta($consultaSql);
    if (is_null($datos))
        return null;
    $consultaSql = "SELECT * FROM Sala WHERE nombreCine = ?";
    foreach ($datos as $k => $v)
        $datos[$k]['salas'] = array_column(consultaClave($consultaSql, [$v['nombreCine']]), 'numeroSala');
    return $datos;
}



// Funciones de acceso a la base de datos.

// Realiza la consulta requerida en la base de datos y devuelve un array que contiene los datos solicitados. Este método se utiliza cuando se traen todos los elementos de una tabla.
function consulta($consultaSql)
{
    global $con;
    try {
        $statement = $con->prepare($consultaSql);
        $statement->execute();
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC))
            $datos[] = $fila;
    } catch (PDOException $pe) {
        return null;
    }
    return (isset($datos) && !blank($datos)) ?
        $datos : null;
}

// Realiza la consulta requerida en la base de datos y devuelve un array que contiene los datos solicitados. Este método se utiliza cuando se traen varios elementos de una tabla según un valor clave.
function consultaClave($consultaSql, $clave)
{
    global $con;
    try {
        $statement = $con->prepare($consultaSql);
        $statement->execute($clave);
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC))
            $datos[] = $fila;
    } catch (PDOException $pe) {
        return null;
    }
    return (isset($datos) && !blank($datos)) ?
        $datos : null;
}

// Realiza la consulta requerida en la base de datos y devuelve un array que contiene los datos solicitados. Este método se utiliza cuando se trae un solo elemento de una tabla según un valor clave.
function consultaUnica($consultaSql, $clave)
{
    global $con;
    try {
        $statement = $con->prepare($consultaSql);
        $statement->execute($clave);
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC))
            $datos[] = $fila;
    } catch (PDOException $pe) {
        return null;
    }
    return (isset($datos) && !blank($datos)) ?
        $datos[0] : null;
}