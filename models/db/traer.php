<?php

// Este script devuelve los datos requeridos por diferentes scripts.

require_once "conn.php";
require_once "../../models/utilities/validacion.php";

// Función de Verificación

function existe($key, $tabla, $val)
{
    $consultaSql = "SELECT $key FROM $tabla WHERE $key = ?";
    $datos = consultaUnica($consultaSql, [$val]);
    return !is_null($datos);
}

// Funciones de Inicio de Sesión

// Devuelve la contraseña asociada al usuario poseedor del email ingresado.
function traerPasswd($email)
{
    $consultaSql = "SELECT passwd FROM Usuario WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    return (!is_null($datos) && isset($datos['passwd'])) ?
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
    $rol = consultaUnica($consultaSql, [$email, $email])['rol'] ?? null;
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
    return (!is_null($datos) && !blank($datos['token'])) ?
        $datos['token'] : null;
}

// Devuelve una lista de los clientes con cuenta.
function traerClientes()
{
    $consultaSql = "SELECT email FROM Cliente";
    if (!is_null(consulta($consultaSql))) {
        foreach (consulta($consultaSql) as $x)
            $ids[] = $x['email'];
    } else {
        return null;
    }
    foreach ($ids as $x)
        $datos[] = traerUsuario($x);
    return $datos;
}

function traerCC($email)
{
    $consultaSql = "SELECT numeroTarjeta, banco FROM Cliente WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    foreach (['numeroTarjeta', 'banco'] as $x)
        if (blank($datos[$x]))
            return null;
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve una lista de los empleados.
function traerEmpleados($rol)
{
    $consultaSql = "SELECT email FROM Empleado WHERE esAdmin = ?";
    if (!is_null(consultaClave($consultaSql, [$rol]))) {
        foreach (consultaClave($consultaSql, [$rol]) as $x)
            $ids[] = $x['email'];
    } else {
        return null;
    }
    foreach ($ids as $x)
        $datos[] = traerUsuario($x);
    return $datos;
}

// Devuelve una lista de imagenes de perfil.
function traerPFP()
{
    $consultaSql = "SELECT * FROM ImagenPerfil";
    $datos = consulta($consultaSql);
    return (!is_null($datos)) ?
        array_column($datos, 'imagenPerfil') : null;
}



// Funciones de datos referidos a los productos

// Devuelve la pelicula asociada al ID ingresado.
function traerPelicula($idProducto)
{
    $consultaSql = "SELECT * FROM Pelicula WHERE idProducto = ?";
    $datos = consultaUnica($consultaSql, [$idProducto]);
    if (is_null($datos))
        return null;
    $consultas = [
        'nombreCategoria' => "SELECT nombreCategoria FROM tieneCategorias WHERE idProducto = ?",
        'dimension' => "SELECT dimension FROM tieneDimensiones WHERE idProducto = ?",
        'idioma' => "SELECT idioma FROM tieneIdiomas WHERE idProducto = ?"
    ];
    foreach ($consultas as $k => $v)
        if (($res = consultaClave($v, [$idProducto])) && is_array($res))
            $datos[$k] = array_column($res, $k);
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
        if (($res = consultaClave($v, [$datos['idProducto']])) && is_array($res))
            $datos[$k] = array_column($res, $k);
    return $datos;
}

// Devuelve todas las peliculas.
function traerPeliculas()
{
    $consultaSql = "SELECT idProducto FROM Pelicula";
    foreach ((consulta($consultaSql) ?? []) as $x)
        $ids[] = $x['idProducto'];
    if (!isset($ids))
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
        array_column($datos, 'nombreCategoria') : null;
}

function traerPorCategoria($categoria)
{
    $consultaSql = "SELECT idProducto FROM tieneCategorias WHERE nombreCategoria = ?";
    if (!is_null($arr = consultaClave($consultaSql, [$categoria])))
        foreach ($arr as $x)
            if (in_array($x['idProducto'], array_column(traerIdCartelera(), 'idProducto')))
                $datos[] = traerPelicula($x['idProducto']);
    return isset($datos) ?
        $datos : null;
}

// Devuelve las dimensiones disponibles.
function traerDimensiones()
{
    $consultaSql = "SELECT dimension FROM Dimensiones";
    $datos = consulta($consultaSql);
    return (!is_null($datos)) ?
        array_column($datos, 'dimension') : null;
}

// Devuelve el precio de la dimensión solicitada.
function traerPrecioD($dim)
{
    $consultaSql = "SELECT precio FROM Dimensiones WHERE dimension = ?";
    $datos = consultaUnica($consultaSql, [$dim]);
    return (!is_null($datos)) ?
        $datos['precio'] : null;
}

// Devuelve los idiomas disponibles.
function traerIdiomas()
{
    $consultaSql = "SELECT * FROM Idiomas";
    $datos = consulta($consultaSql);
    return (!is_null($datos)) ?
        array_column($datos, 'idioma') : null;
}

// Devuelve el artículo asociada al ID ingresado.
function traerArticulo($idProducto)
{
    $consultaSql = "SELECT * FROM Articulo WHERE idProducto = ?";
    $datos = consultaUnica($consultaSql, [$idProducto]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve el artículo asociada al nombre ingresado.
function traerArticuloNombre($n)
{
    $consultaSql = "SELECT * FROM Articulo WHERE nombreArticulo = ?";
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
        if (in_array($x, traerIdCartelera()))
            $datos[] = traerPelicula($x['idProducto']);
    return !blank($datos) ? 
        $datos : null;
}

// Devuelve una lista de imagenes del slider.
function traerSlider()
{
    $consultaSql = "SELECT * FROM ImagenSlider";
    $datos = consulta($consultaSql);
    return (!is_null($datos)) ?
        array_column($datos, 'imagenSlider') : null;
}

// Trae los registros de compra asociados a esa función.
function traerRegistro($idFuncion)
{
    $consultaSql = "SELECT * FROM Compra WHERE idFuncion = ?";
    $datos = consultaClave($consultaSql, [$idFuncion]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Trae los registros de compra asociados a ese ID.
function traerCompra($idCompra)
{
    $consultaSql = "SELECT * FROM Compra WHERE idCompra = ?";
    $datos = consultaUnica($consultaSql, [$idCompra]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve los datos del carrito.
function traerCarrito($email)
{
    $consultaSql = "SELECT * FROM Carrito WHERE email = ?";
    $datos = consultaUnica($consultaSql, [$email]);
    $consultaSql = "SELECT idProducto, cantidad FROM CarritoArticulo WHERE email = ?";
    $art = consultaClave($consultaSql, [$email]);
    if (!is_null($art)) {
        $datos['articulos'] = $art;
        if (!isset($datos['email']))
            $datos['email'] = $email;
    }
    return !is_null($datos) ?
        $datos : null;
}

// Devuelve una función específica.
function traerFunc($idFuncion)
{
    $consultaSql = "SELECT * FROM Funciones WHERE idFuncion = ?";
    $datos = consultaUnica($consultaSql, [$idFuncion]);
    if (is_null($datos)) 
        return null;
    $datos['asientos'] = traerAsientosReservados($idFuncion);
    $sala = traerSala($datos['nombreCine'], $datos['numeroSala']);
    $datos['disp'] = $sala['disp'] - count(traerAsientosComprados($idFuncion) ?? []);
    return $datos;
}

// Devuelve todas las funciones.
function traerFuncLista()
{
    $consultaSql = "SELECT * FROM Funciones";
    $datos = consulta($consultaSql);
    if (is_null($datos)) 
        return null;
    for ($i = 0; $i < count($datos); $i++) {
        $datos[$i]['asientos'] = traerAsientosReservados($datos[$i]['idFuncion']);
        $sala = traerSala($datos[$i]['nombreCine'], $datos[$i]['numeroSala']);
        $datos[$i]['disp'] = $sala['disp'] - count(traerAsientosComprados($datos[$i]['idFuncion']) ?? []);
    }
    return $datos;
}

// Devuelve las funciones de cierta película.
function traerFuncEsp($idProducto)
{
    $consultaSql = "SELECT * FROM Funciones WHERE idProducto = ?";
    $datos = consultaClave($consultaSql, [$idProducto]);
    if (is_null($datos)) 
        return null;
    for ($i = 0; $i < count($datos); $i++) {
        $datos[$i]['asientos'] = traerAsientosReservados($datos[$i]['idFuncion']);
        $sala = traerSala($datos[$i]['nombreCine'], $datos[$i]['numeroSala']);
        $datos[$i]['disp'] = $sala['disp'] - count(traerAsientosComprados($datos[$i]['idFuncion']) ?? []);
    }
    return $datos;
}

// Devuelve las funciones programadas para cierta fecha.
function traerFuncFecha($fecha)
{
    $consultaSql = "SELECT * FROM Funciones WHERE fechaPelicula = ?";
    $datos = consultaClave($consultaSql, [$fecha]);
    if (is_null($datos)) 
        return null;
    for ($i = 0; $i < count($datos); $i++) {
        $datos[$i]['asientos'] = traerAsientosReservados($datos[$i]['idFuncion']);
        $sala = traerSala($datos[$i]['nombreCine'], $datos[$i]['numeroSala']);
        $datos[$i]['disp'] = $sala['disp'] - count(traerAsientosComprados($datos[$i]['idFuncion']) ?? []);
    }
    return $datos;
}

// Devuelve las funciones programadas desde el día actual (incluido) en adelante.
function traerFuncFuturas()
{
    $fecha = new DateTime('now', new DateTimeZone('America/Montevideo'));
    $consultaSql = "SELECT * FROM Funciones WHERE fechaPelicula >= ? ORDER BY fechaPelicula, horaPelicula ASC";
    $datos = consultaClave($consultaSql, [$fecha->format('Y-m-d')]);
    if (is_null($datos)) 
        return null;
    for ($i = 0; $i < count($datos); $i++) {
        $datos[$i]['asientos'] = traerAsientosReservados($datos[$i]['idFuncion']);
        $sala = traerSala($datos[$i]['nombreCine'], $datos[$i]['numeroSala']);
        $datos[$i]['disp'] = $sala['disp'] - count(traerAsientosComprados($datos[$i]['idFuncion']) ?? []);
    }
    return $datos;
}

// Devuelve las funciones programadas para una pelicula en específico desde el día actual (incluido) en adelante.
function traerFuncFuturasEsp($idProducto)
{
    $fecha = new DateTime('now', new DateTimeZone('America/Montevideo'));
    $consultaSql = "SELECT * FROM Funciones WHERE idProducto = ? AND fechaPelicula >= ?";
    $datos = consultaClave($consultaSql, [$idProducto, $fecha->format('Y-m-d')]);
    if (is_null($datos)) 
        return null;
    for ($i = 0; $i < count($datos); $i++) {
        $datos[$i]['asientos'] = traerAsientosReservados($datos[$i]['idFuncion']);
        $sala = traerSala($datos[$i]['nombreCine'], $datos[$i]['numeroSala']);
        $datos[$i]['disp'] = $sala['disp'] - count(traerAsientosComprados($datos[$i]['idFuncion']) ?? []);
    }
    return $datos;
}

// Devuelve los asientos reservados en una funcion.
function traerAsientosReservados($idFuncion)
{
    $consultaSql = "SELECT fila, columna FROM Asientos WHERE idFuncion = ?";
    $datos = consultaClave($consultaSql, [$idFuncion]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve los asientos comprados en una funcion.
function traerAsientosComprados($idFuncion)
{
    $consultaSql = "SELECT fila, columna FROM Asientos WHERE idFuncion = ? AND vendido = 1";
    $datos = consultaClave($consultaSql, [$idFuncion]);
    return (!is_null($datos)) ?
        $datos : null;
}

// Devuelve todos los cines.
function traerCines()
{
    $consultaSql = "SELECT * FROM Cine";
    $datos = consulta($consultaSql);
    if (is_null($datos))
        return null;
    $consultaSql = "SELECT numeroSala, largo, ancho FROM Sala WHERE nombreCine = ?";
    foreach ($datos as $k => $v)
        $datos[$k]['salas'] = consultaClave($consultaSql, [$v['nombreCine']]);
    return $datos;
}

// Devuelve la información de un cine en específico.
function traerCine($nombreCine)
{
    $consultaSql = "SELECT * FROM Cine WHERE nombreCine = ?";
    $datos = consultaUnica($consultaSql, [$nombreCine]);
    if (is_null($datos))
        return null;
    $consultaSql = "SELECT numeroSala, largo, ancho FROM Sala WHERE nombreCine = ?";
    $datos['salas'] = consultaClave($consultaSql, [$datos['nombreCine']]);
    return $datos;
}

// Devuelve la disposición de una sala en específico.
function traerSala($nombreCine, $numeroSala)
{
    $consultaSql = "SELECT ancho, largo, disp FROM Sala WHERE nombreCine = ? AND numeroSala = ?";
    $datos = consultaUnica($consultaSql, [$nombreCine, $numeroSala]);
    return $datos;
}

// Devuelve la información de cinemática.
function traerEmpresa()
{
    $consultaSql = "SELECT * FROM Empresa WHERE nombreEmpresa = ?";
    $datos = consultaUnica($consultaSql, ['Cinematica']);
    return $datos;
}

// Devuelve el contenido de un mail.
function traerMail($asunto)
{
    $consultaSql = "SELECT cabecera, cuerpo FROM Mail WHERE asunto = ?";
    $datos = consultaUnica($consultaSql, [$asunto]);
    return $datos;
}

// Trae la lista de todos los candidatos con CV.
function traerListaCVs()
{
    $consultaSql = "SELECT * FROM CV";
    $datos = consulta($consultaSql);
    return $datos;
}

// Trae los detalles de un CV.
function traerCV($documento)
{
    $consultaSql = "SELECT * FROM CV WHERE documento = ?";
    $datos = consultaUnica($consultaSql, [$documento]);
    return $datos;
}

function traerRS()
{
    $consultaSql = "SELECT * FROM RedesSociales";
    $datos = consulta($consultaSql);
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
        print $pe;
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
        $statement->execute(array_values($clave));
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC))
            $datos[] = $fila;
    } catch (PDOException $pe) {
        print $pe;
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
        $statement->execute(array_values($clave));
        while ($fila = $statement->fetch(PDO::FETCH_ASSOC))
            $datos[] = $fila;
    } catch (PDOException $pe) {
        print $pe;
        return null;
    }
    return (isset($datos) && !blank($datos)) ?
        $datos[0] : null;
}