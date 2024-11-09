<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .factura-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .factura-container h2 {
            text-align: center;
        }
        .section {
            margin-bottom: 15px;
        }
        .section h3 {
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        .items-table, .articles-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th, .items-table td,
        .articles-table th, .articles-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="factura-container">
    <h2>Factura de Compra</h2>

    <div class="section">
        <h3>Cliente</h3>
        <p>Nombre: <?php echo $datos['cliente']; ?></p>
        <p>Fecha de Compra: <?php echo $datos['fechaCompra']; ?></p>
        <p>Identificador: <?php echo $datos['idCompra']; ?></p>
    </div>

    <?php if (isset($datos['pelicula'])): ?>
    <div class="section">
        <h3>Detalles de la Función</h3>
        <table class="items-table">
            <tr>
                <th>Película</th>
                <td><?php echo $datos['pelicula']['nombrePelicula']; ?></td>
            </tr>
            <tr>
                <th>Fecha</th>
                <td><?php echo $datos['pelicula']['fecha']; ?></td>
            </tr>
            <tr>
                <th>Hora</th>
                <td><?php echo $datos['pelicula']['hora']; ?></td>
            </tr>
            <tr>
                <th>Cine</th>
                <td><?php echo $datos['pelicula']['cine']; ?></td>
            </tr>
            <tr>
                <th>Sala</th>
                <td><?php echo $datos['pelicula']['sala']; ?></td>
            </tr>
            <tr>
                <th>Asientos</th>
                <td><?php echo "Fila: " . str_replace(["-", ", "], [" Columna: ", "<br> Fila: "], $datos['pelicula']['asientos']); ?></td>
            </tr>
            <tr>
                <th>Precio</th>
                <td><?php echo "$" . number_format($datos['pelicula']['precio'], 2); ?></td>
            </tr>
        </table>
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
                <td><?php echo $articulo['nombreArticulo']; ?></td>
                <td><?php echo $articulo['cantidad']; ?></td>
                <td><?php echo "$" . number_format($articulo['precio'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

    <div class="section total">
        <p>Precio Total: <?php echo "$" . number_format($datos['precioFinal'], 2); ?></p>
    </div>
</div>

</body>
</html>