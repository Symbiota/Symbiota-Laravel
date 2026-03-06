<?php

return [
    'LOADER' => 'Glossary Term Loader',
    'GLOSS_MGMNT' => 'Glossary Management',
    'BATCH_LOAD' => 'Descarga de Glosario por Lote',
    'G_BATCH_LOAD' => 'Descarga de Términos de Glosario por Lote',
    'BATCH_EXPLAIN' => 'Esta página permite que un Administrador Taxonómico cargue archivos de glosario por lote.',
    'UPLOAD_FORM' => 'Formulario de Carga de Términos',
    'SOURCE_FIELD' => 'Campo de Origen',
    'TARGET_FIELD' => 'Campo Objetivo',
    'UNMAPPED' => 'Campo sin Mapear',
    'LEAVE_UNMAPPED' => 'Dejar Campo sin Mapear',
    'TRANSFER_TERMS' => 'Transferir Términos a la Tabla Central',
    'REVIEW_STATS' => 'Revise las estadísticas de abajo antes de activar. Use la opción de descarga para revisar y/o ajustar para recarga si es necesario.',
    'TERMS_UPLOADED' => 'Términos cargados',
    'TOTAL_TERMS' => 'Términos Totales',
    'IN_DB' => 'Términos ya en la base de datos',
    'NEW_TERMS' => 'Nuevos términos',
    'UNAVAILABLE' => 'Estadísticas de carga no están disponibles',
    'DOWNLOAD_TERMS' => 'Descargar Archivo CSV de Términos',
    'TERM_SUCCESS' => 'Carga de términos aparentemente fue exitosa',
    'G_SEARCH' => 'Búsqueda en Glosario',
    'TO_SEARCH' => 'para buscar por un nombre cargado.',
    'UPLOAD_EXPLAIN' => 'Archivos de texto planos CSV (delimitados por coma) pueden ser cargados aquí.
						Por favor especifique el grupo taxonómico al cual los términos serán relacionados.
						Si su archivo contiene términos en múltiples idiomas, etiquete cada columna de términos con el idioma en el que se encuentran (e.g., Español),
						y luego nombre todas las columnas relacionadas a ese término como el lenguaje, guión bajo, y luego el nombre de la columna
						(e.g., Español, Español_definición, Inglés, Inglés_definición, etc.). Columnas pueden ser añadidas para la definición,
						autor, traductor, fuente, notas, y recursos en línea con url.
						Sinónimos pueden ser añadidos nombrando la columna con el lenguaje, guión bajo, sinónimo (e.g., Español_sinónimo).
						Una fuente puede ser añadida para todos los términos rellenando la casilla Introducir Recursos de abajo.
						Por favor no use espacios en los nombres de las columnas ni en los nombres de los archivos.
						Si el paso de la carga de archivos falla sin desplegar un mensaje de error, es posible que el 
						tamaño del archivo sea mayor al límite permitido dentro de su instalación PHP (ver su archivo de configuración php).',
    'ENTER_TAXON' => 'Agregar Grupo Taxonómico',
    'ENTER_SOURCE' => 'Agregar Fuentes',
    'UPLOAD' => 'Cargar Archivo',
];
