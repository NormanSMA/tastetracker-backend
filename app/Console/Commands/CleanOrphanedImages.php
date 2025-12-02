<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class CleanOrphanedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina imágenes de productos que ya no existen en la BD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando imágenes huérfanas...');

        // Obtener todas las imágenes de productos de la BD
        $dbImages = Product::pluck('image')->filter()->toArray();

        // Extraer solo los nombres de archivo (quitar 'products/' si está presente)
        $dbImageNames = array_map(function($image) {
            return basename($image);
        }, $dbImages);

        // Obtener todos los archivos en storage/app/public/products
        $diskFiles = Storage::disk('public')->files('products');

        // Contador de archivos eliminados
        $deletedCount = 0;

        // Comparar: Si un archivo del disco no está en el array de la BD, eliminarlo
        foreach ($diskFiles as $file) {
            $fileName = basename($file);

            if (!in_array($fileName, $dbImageNames)) {
                Storage::disk('public')->delete($file);
                $this->warn("✗ Eliminado: {$fileName}");
                $deletedCount++;
            }
        }

        // Mostrar resultado
        if ($deletedCount > 0) {
            $this->info("✓ Se eliminaron {$deletedCount} archivo(s) huérfano(s).");
        } else {
            $this->info('✓ No se encontraron imágenes huérfanas.');
        }

        return 0;
    }
}
