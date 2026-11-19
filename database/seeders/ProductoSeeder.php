<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Seed de categorías y 35 productos variados para la Práctica 1.4.
     */
    public function run(): void
    {
        $nombresCategorias = [
            'Electrónica',
            'Periféricos',
            'Mobiliario',
            'Accesorios',
            'Software',
        ];

        $categorias = [];
        foreach ($nombresCategorias as $nombre) {
            $categorias[$nombre] = Categoria::firstOrCreate(['nombre' => $nombre]);
        }

        $productos = [
            // Electrónica (7)
            ['nombre' => 'Laptop Dell Inspiron 15', 'descripcion' => 'Portátil de 15.6 pulgadas con procesador Intel Core i5, 16 GB RAM y SSD de 512 GB.', 'categoria' => 'Electrónica', 'precio' => 14500.00, 'existencias' => 12, 'estado' => 'Disponible'],
            ['nombre' => 'Monitor LG UltraWide 29"', 'descripcion' => 'Monitor IPS Full HD panorámico 21:9 ideal para productividad y programación.', 'categoria' => 'Electrónica', 'precio' => 5499.00, 'existencias' => 8, 'estado' => 'Disponible'],
            ['nombre' => 'Tablet Samsung Galaxy Tab A9', 'descripcion' => 'Tableta de 11 pulgadas con 128 GB de almacenamiento y batería de larga duración.', 'categoria' => 'Electrónica', 'precio' => 4299.50, 'existencias' => 15, 'estado' => 'Disponible'],
            ['nombre' => 'Mini PC Intel NUC 13', 'descripcion' => 'Computadora compacta de bajo consumo con Wi-Fi 6E y puertos Thunderbolt.', 'categoria' => 'Electrónica', 'precio' => 9800.00, 'existencias' => 0, 'estado' => 'Agotado'],
            ['nombre' => 'Proyector Epson PowerLite X49', 'descripcion' => 'Proyector 3LCD de 3600 lúmenes para aulas y salas de juntas.', 'categoria' => 'Electrónica', 'precio' => 11200.00, 'existencias' => 4, 'estado' => 'Disponible'],
            ['nombre' => 'Impresora Multifuncional HP Smart Tank', 'descripcion' => 'Impresora de tanque de tinta continua con conectividad inalámbrica.', 'categoria' => 'Electrónica', 'precio' => 3890.00, 'existencias' => 9, 'estado' => 'Disponible'],
            ['nombre' => 'No-Break CyberPower 1000VA', 'descripcion' => 'Respaldo de energía UPS con regulador de voltaje y pantalla LCD.', 'categoria' => 'Electrónica', 'precio' => 2650.00, 'existencias' => 0, 'estado' => 'Agotado'],

            // Periféricos (7)
            ['nombre' => 'Teclado Mecánico Keychron K2', 'descripcion' => 'Teclado inalámbrico compacto 75% con switches táctiles y retroiluminación blanca.', 'categoria' => 'Periféricos', 'precio' => 1850.00, 'existencias' => 20, 'estado' => 'Disponible'],
            ['nombre' => 'Mouse Logitech MX Master 3S', 'descripcion' => 'Ratón ergonómico inalámbrico con rueda electromagnética MagSpeed y sensor de 8000 DPI.', 'categoria' => 'Periféricos', 'precio' => 2199.00, 'existencias' => 14, 'estado' => 'Disponible'],
            ['nombre' => 'Cámara Web Logitech C920 HD Pro', 'descripcion' => 'Webcam Full HD 1080p con enfoque automático y micrófonos estéreo integrados.', 'categoria' => 'Periféricos', 'precio' => 1490.00, 'existencias' => 18, 'estado' => 'Disponible'],
            ['nombre' => 'Audífonos Sony WH-CH720N', 'descripcion' => 'Diadema inalámbrica con cancelación activa de ruido y hasta 35 horas de batería.', 'categoria' => 'Periféricos', 'precio' => 2799.00, 'existencias' => 6, 'estado' => 'Disponible'],
            ['nombre' => 'Micrófono USB HyperX SoloCast', 'descripcion' => 'Micrófono de condensador cardioide con sensor de silencio táctil.', 'categoria' => 'Periféricos', 'precio' => 1150.00, 'existencias' => 0, 'estado' => 'Agotado'],
            ['nombre' => 'Escáner de Documentos Brother ADS', 'descripcion' => 'Escáner dúplex de alta velocidad para digitalización de expedientes.', 'categoria' => 'Periféricos', 'precio' => 6250.00, 'existencias' => 3, 'estado' => 'Disponible'],
            ['nombre' => 'Tableta Digitalizadora Wacom Intuos', 'descripcion' => 'Tableta gráfica con lápiz sensible a la presión ideal para diseño y clases en línea.', 'categoria' => 'Periféricos', 'precio' => 1950.00, 'existencias' => 11, 'estado' => 'Disponible'],

            // Mobiliario (7)
            ['nombre' => 'Silla Ergonómica de Malla Ejecutiva', 'descripcion' => 'Silla con soporte lumbar ajustable, descansabrazos 3D y base metálica.', 'categoria' => 'Mobiliario', 'precio' => 3450.00, 'existencias' => 10, 'estado' => 'Disponible'],
            ['nombre' => 'Escritorio Elevable Eléctrico 140cm', 'descripcion' => 'Escritorio Standing Desk con memoria de 4 alturas y estructura de acero.', 'categoria' => 'Mobiliario', 'precio' => 6890.00, 'existencias' => 5, 'estado' => 'Disponible'],
            ['nombre' => 'Archivero Metálico de 3 Gavetas', 'descripcion' => 'Mueble archivador con cerradura centralizada y rieles de extensión completa.', 'categoria' => 'Mobiliario', 'precio' => 2890.00, 'existencias' => 7, 'estado' => 'Disponible'],
            ['nombre' => 'Librero Modular de Madera 5 Niveles', 'descripcion' => 'Estantería vertical resistente en acabado nogal para oficina o estudio.', 'categoria' => 'Mobiliario', 'precio' => 1780.00, 'existencias' => 0, 'estado' => 'Agotado'],
            ['nombre' => 'Mesa de Juntas Rectangular 6 Personas', 'descripcion' => 'Cubierta de melamina antirrayaduras con pasacables central integrado.', 'categoria' => 'Mobiliario', 'precio' => 7950.00, 'existencias' => 2, 'estado' => 'Disponible'],
            ['nombre' => 'Lámpara de Escritorio LED Regulable', 'descripcion' => 'Brazo articulado con control táctil de brillo y temperatura de color.', 'categoria' => 'Mobiliario', 'precio' => 640.00, 'existencias' => 25, 'estado' => 'Disponible'],
            ['nombre' => 'Pizarrón Blanco Magnético 120x90cm', 'descripcion' => 'Superficie porcelanizada con marco de aluminio y bandeja para marcadores.', 'categoria' => 'Mobiliario', 'precio' => 1320.00, 'existencias' => 13, 'estado' => 'Disponible'],

            // Accesorios (7)
            ['nombre' => 'Hub USB-C 8 en 1 con HDMI 4K', 'descripcion' => 'Adaptador multipuerto con HDMI, Ethernet Gigabit, lector SD y carga PD 100W.', 'categoria' => 'Accesorios', 'precio' => 790.00, 'existencias' => 30, 'estado' => 'Disponible'],
            ['nombre' => 'Mochila Antirrobo para Laptop 15.6"', 'descripcion' => 'Mochila impermeable con compartimento acolchado y puerto USB externo.', 'categoria' => 'Accesorios', 'precio' => 899.00, 'existencias' => 22, 'estado' => 'Disponible'],
            ['nombre' => 'Base Enfriadora para Laptop 5 Ventiladores', 'descripcion' => 'Soporte inclinado con iluminación LED y dos puertos USB adicionales.', 'categoria' => 'Accesorios', 'precio' => 480.00, 'existencias' => 19, 'estado' => 'Disponible'],
            ['nombre' => 'Disco Duro Externo Seagate 2TB', 'descripcion' => 'Almacenamiento portátil USB 3.0 compatible con Windows, macOS y Linux.', 'categoria' => 'Accesorios', 'precio' => 1390.00, 'existencias' => 16, 'estado' => 'Disponible'],
            ['nombre' => 'Memoria USB Kingston DataTraveler 128GB', 'descripcion' => 'Unidad flash USB 3.2 Gen 1 con carcasa metálica resistente.', 'categoria' => 'Accesorios', 'precio' => 245.00, 'existencias' => 45, 'estado' => 'Disponible'],
            ['nombre' => 'Brazo Articulado para Monitor Dual', 'descripcion' => 'Soporte de escritorio con pistón de gas para monitores de 17 a 32 pulgadas.', 'categoria' => 'Accesorios', 'precio' => 1120.00, 'existencias' => 0, 'estado' => 'Agotado'],
            ['nombre' => 'Tapete Desk Mat de Cuero Sintético', 'descripcion' => 'Protector antideslizante impermeable de 80x40 cm para escritorio.', 'categoria' => 'Accesorios', 'precio' => 320.00, 'existencias' => 34, 'estado' => 'Disponible'],

            // Software (7)
            ['nombre' => 'Licencia Microsoft 365 Personal (1 Año)', 'descripcion' => 'Suscripción anual con Word, Excel, PowerPoint y 1 TB en OneDrive.', 'categoria' => 'Software', 'precio' => 1299.00, 'existencias' => 50, 'estado' => 'Disponible'],
            ['nombre' => 'Licencia JetBrains PhpStorm Anual', 'descripcion' => 'Entorno de desarrollo profesional para PHP, Laravel, bases de datos y frontend.', 'categoria' => 'Software', 'precio' => 2150.00, 'existencias' => 15, 'estado' => 'Disponible'],
            ['nombre' => 'Antivirus ESET NOD32 3 Dispositivos', 'descripcion' => 'Protección ligera contra malware, ransomware y phishing por 12 meses.', 'categoria' => 'Software', 'precio' => 750.00, 'existencias' => 40, 'estado' => 'Disponible'],
            ['nombre' => 'Licencia Windows 11 Pro Digital', 'descripcion' => 'Clave de activación original para equipos empresariales y de desarrollo.', 'categoria' => 'Software', 'precio' => 3199.00, 'existencias' => 0, 'estado' => 'Agotado'],
            ['nombre' => 'Adobe Creative Cloud Fotografía', 'descripcion' => 'Plan anual que incluye Photoshop, Lightroom y 20 GB de almacenamiento en nube.', 'categoria' => 'Software', 'precio' => 2388.00, 'existencias' => 18, 'estado' => 'Disponible'],
            ['nombre' => 'Licencia TablePlus Standard', 'descripcion' => 'Cliente nativo rápido para administrar PostgreSQL, MySQL y SQLite.', 'categoria' => 'Software', 'precio' => 1690.00, 'existencias' => 12, 'estado' => 'Disponible'],
            ['nombre' => 'Suscripción Notion Plus Académico', 'descripcion' => 'Espacio colaborativo para documentación técnica, wikis y tableros de tareas.', 'categoria' => 'Software', 'precio' => 960.00, 'existencias' => 28, 'estado' => 'Disponible'],
        ];

        foreach ($productos as $index => $datos) {
            Producto::firstOrCreate(
                ['nombre' => $datos['nombre']],
                [
                    'descripcion' => $datos['descripcion'],
                    'categoria_id' => $categorias[$datos['categoria']]->id,
                    'precio' => $datos['precio'],
                    'existencias' => $datos['existencias'],
                    'estado' => $datos['estado'],
                    'created_at' => now()->subDays(35 - $index),
                    'updated_at' => now()->subDays(35 - $index),
                ]
            );
        }
    }
}
