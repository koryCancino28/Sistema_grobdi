@extends('adminlte::page')

@section('title', 'Reporte Clasificación')

@section('content_header')
    <!-- <h1>Pedidos</h1> -->
@stop

@section('content')
<div class="content-container">
    <div class="container">
        <div class="cont-report">
            <h1>Reporte de Clasificaciones y Montos Totales</h1>

            <!-- Filtro de Mes -->
            <form class="form-graf" method="get" action="{{ route('muestras.reporte') }}">
                <label for="mes">Seleccionar mes:</label>
                <input type="month" name="mes" id="mes" value="{{ $mesSeleccionado }}">
                <button type="submit">Filtrar</button>
            </form>

            <!-- Gráfico de Barras -->
            <div class="chart-container">
                <canvas id="graficoBarras"></canvas>
            </div>

            <!-- Tabla de Clasificaciones y Monto Total -->
            <h3>Tabla de Clasificaciones y Monto Total</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Clasificación</th>
                            <th>Cantidad</th>
                            <th>Monto Total (S/)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($muestrasData as $data)
                            <tr>
                                <td>{{ $data['nombre_clasificacion'] }}</td>
                                <td>{{ $data['cantidad'] }}</td>
                                <td>{{ number_format($data['monto_total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    @stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/muestras/Reporte.css') }}">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        // Configuración del gráfico con colores del tema
        const primaryColor = '#d6254d';
        const secondaryColor = '#ff5475';
        const accentColor = '#fdeba9';

        // Obtener los datos pasados desde el controlador
        const clasificaciones = @json($clasificacionLabels);
        const montosTotales = @json($montosTotales);
        const cantidadTotal = @json($cantidadTotal);

        // Verificar si hay datos vacíos o nulos
        if (clasificaciones.length === 0 || montosTotales.length === 0 || cantidadTotal.length === 0) {
            alert('No se encontraron datos para mostrar.');
        }

        // Configuración del gráfico de barras
        const ctx = document.getElementById('graficoBarras').getContext('2d');
        const graficoBarras = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: clasificaciones,
                datasets: [{
                    label: 'Monto Total en Soles',
                    data: montosTotales,
                    backgroundColor: secondaryColor,
                    borderColor: primaryColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'S/ ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                const index = tooltipItem.dataIndex;
                                const monto = tooltipItem.raw;
                                const cantidad = cantidadTotal[index];
                                return 'Cantidad: ' + cantidad + ' - Monto Total: S/ ' + monto.toLocaleString();
                            }
                        }
                    },
                    legend: {
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                onClick: function (e) {
                    var activePoints = graficoBarras.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);
                    if (activePoints.length > 0) {
                        var firstPoint = activePoints[0];
                        var value = graficoBarras.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
                        alert('Monto Total: S/ ' + value.toLocaleString());
                    }
                }
            }
        });

        // Función para redimensionar el gráfico al cambiar el tamaño de la ventana
        window.addEventListener('resize', function() {
            graficoBarras.resize();
        });
    </script>
@stop