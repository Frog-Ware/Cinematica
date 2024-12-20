<?php

// Este script devuelve los datos requeridos por diferentes scripts.

require_once "conn.php";
//require_once "../config/acceso.php";

// Funciones de Inicio de Sesión

// Devuelve la contraseña asociada al usuario poseedor del email ingresado.
function traerPasswd($email)
{
    $consultaSql = "SELECT passwd FROM Usuario WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    return (isset($datos['passwd']) && !empty($datos)) ?
        $datos['passwd'] : null;
}

// Devuelve los datos asociados al usuario poseedor del email ingresado.
function traerUsuario($email)
{
    $consultaSql = "SELECT email, nombre, apellido, imagenPerfil FROM Usuario WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve el rol del usuario en cuestión.
function traerRol($email)
{
    $consultaSql = "SELECT email, 1 AS rol FROM Administrador WHERE email = ? UNION ALL SELECT email, 0 AS rol FROM Cliente WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email, $email]);
    return (!empty($datos) && isset($datos['rol'])) ?
        $datos['rol'] : null;
}

// Devuelve el token de cambio de contraseña del usuario en cuestión.
function traerToken($email)
{
    $consultaSql = "SELECT token FROM Usuario WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    return (!empty($datos) && isset($datos['token'])) ?
        $datos['token'] : null;
}



// Funciones de datos referidos a los productos

// Devuelve la pelicula asociada al ID ingresado.
function traerPelicula($id, $campos)
{
    $consultaSql = "SELECT $campos FROM Pelicula WHERE idProducto = ?";
    $datos = consultaUnica($consultaSql, [$id]);
    if (empty($datos))
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
function traerPeliculaNombre($n, $campos)
{
    $consultaSql = "SELECT $campos FROM Pelicula WHERE nombrePelicula = ?";
    $datos = consultaUnica($consultaSql, [$n]);
    if (empty($datos))
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
function traerPeliculas($campos)
{
    $consultaSql = "SELECT idProducto FROM Pelicula";
    foreach (consulta($consultaSql) as $x)
        $ids[] = $x['idProducto'];
    if (empty($ids))
        return null;
    foreach ($ids as $x)
        $datos[] = traerPelicula($x, $campos);
    return $datos;
}

// Devuelve la cartelera de películas en su totalidad.
function traerCartelera($campos)
{
    $consultaSql = "SELECT * FROM Cartelera";
    $ids = consulta($consultaSql);
    if (empty($ids))
        return null;
    foreach ($ids as $x)
        $datos[] = array_merge(traerPelicula($x['idProducto'], $campos), $x);
    return $datos;
}

// Devuelve las categorías disponibles.
function traerCategorias()
{
    $consultaSql = "SELECT * FROM Categorias";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve las dimensiones disponibles.
function traerDimensiones()
{
    $consultaSql = "SELECT dimension FROM Dimensiones";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve los idiomas disponibles.
function traerIdiomas()
{
    $consultaSql = "SELECT * FROM Idiomas";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve el artículo asociada al ID ingresado.
function traerArticulo($idProducto, $campos)
{
    $consultaSql = "SELECT $campos FROM Articulo WHERE idProducto = ?";
    $datos = consultaUnica($consultaSql, [$idProducto]);
    return (!empty($datos)) ?
        $datos : null;
}

// Devuelve la lista de artículos en su totalidad. 
function traerArticulos($campos)
{
    $consultaSql = "SELECT $campos FROM Articulo";
    $datos = consulta($consultaSql);
    return (!empty($datos)) ?
        $datos : null;
}

function traerBusqueda($busqueda, $campos)
{
    $consultaSql = "SELECT idProducto FROM Pelicula WHERE nombrePelicula LIKE ?";
    $id = consultaClave($consultaSql, [$busqueda]);
    if (empty($id))
        return null;
    foreach ($id as $x)
        $datos[] = array_merge(traerPelicula($x['idProducto'], $campos), $x);
    return $datos;
}

function traerRegistro($idProducto)
{
    $consultaSql = "SELECT * FROM Compra WHERE idProducto = ?";
    $datos = consultaClave($consultaSql, [$idProducto]);
    return (!empty($datos)) ?
        $datos : null;
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
    return (!empty($datos)) ?
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
    return (!empty($datos)) ?
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
    return (!empty($datos)) ?
        $datos[0] : null;
}