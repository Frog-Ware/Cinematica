<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: white;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        hr {
            border-color: white;
        }

        p {
            color:  white;
        }

        .din {
            color:  #fccd01;
            left: 150px;
            position: absolute;
        }

        .as {
            color:  #fccd01;
        }

        img {
            max-width: 50px;
            position: absolute;
            left: 480px;
            border-radius: 20%;
        }

        .ticket {
            width: 500px;
            padding: 30px;
            background-color: #000a1c;
            color: white;
            border: 3px dashed #fccd01;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .ticket h1 {
            color: white;
            font-size: 30px;
            text-align: center;
            margin-bottom: 15px;
        }

        .campo {
            margin-top: 10px;
            color: white;
        }


        .cliente h1 {
            color: white;
            display: flex;
        }

        .articulos {
            font-weight: bold;
        }

        .precio-total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            position: relative;
            top: 10px;
            right: 31px;
            padding: 10px;
            color: white;
        }

        .items-table,
        .articles-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 5px 5px 0px 0px;
        }


        .items-table th,
        .items-table td,
        .articles-table th,
        .articles-table td {
            border: 1px solid #000a1c;
            padding: 8px;
            text-align: left;
            color: white;
        }

        .totalplata {
            font-size: 20px;
            text-decoration: underline;
            color: #fccd01;
        }

        .detalles-funcion2 {
            display: flex;
            gap: 5px;
            flex-direction: row;
            justify-content: flex-start;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <img src="../files/logo.png">
        <h1>Factura de Compra</h1>
        <div class="cliente">
            <h2>Cliente</h2>
            <div class="campo"> Nombre: <?php echo $datos['cliente']; ?> </div>
            <div class="campo"> Identificador: <?php echo $datos['idCompra']; ?> </div>
            <div class="campo"> Fecha de compra: <?php echo $datos['fechaCompra']; ?> </div>
            <hr>
        </div>
        <?php if (isset($datos['pelicula'])): ?>
        <div class="detalles-funcion">
            <h3>Detalles de la Función</h3>
            <div class="detalles-funcion2"><p>Película:<span class="din"> <?php echo $datos['pelicula']['nombrePelicula']; ?> </p></div>
            <div class="detalles-funcion2"><p>Fecha<span class="din"> <?php echo $datos['pelicula']['fecha']; ?> </span></p></div>
            <div class="detalles-funcion2"><p>Hora:<span class="din"> <?php echo $datos['pelicula']['hora']; ?> </span></p></div>
            <div class="detalles-funcion2"><p>Cine:<span class="din"> <?php echo $datos['pelicula']['cine']; ?> </span></p></div>
            <div class="detalles-funcion2"><p>Sala:<span class="din"> <?php echo $datos['pelicula']['sala']; ?> </span></p></div>
            <div class="detalles-funcion2"><p>Asientos:</p><p class="as"> <?php echo "Fila: " . str_replace(["-", ", "], [" Columna: ", "<br> Fila: "], $datos['pelicula']['asientos']); ?> </p></div>
            <div class="detalles-funcion2"><p>Precio:<span class="din"> <?php echo "$" . number_format($datos['pelicula']['precio'], 2); ?> </span></p></div>
            <hr>
        </div>
        <?php endif; ?>
        
        <?php if (isset($datos['articulos'])): ?>
        <div class="section">
            <h3>Artículos Adicionales</h3>
            <table class="articles-table">
                <tr>
                    <th>Artículo</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                </tr>
                <?php foreach ($datos['articulos'] as $articulo): ?>
                <tr>
                    <td><?php echo $articulo['nombreArticulo']; ?> </td>
                    <td><?php echo $articulo['cantidad']; ?> </td>
                    <td><?php echo "$" . number_format($articulo['precio'], 2); ?> </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
        <div class="precio-total">
            Precio Total: <span class="totalplata">$ <?php echo number_format($datos['precioFinal'], 2); ?></span>
        </div>
    </div>

</body>

</html>