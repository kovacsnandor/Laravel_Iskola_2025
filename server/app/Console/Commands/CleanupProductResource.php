<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput; // Az Artisan parancsok futtatásához

class CleanupProductResource extends Command
{
    //A parancs neve és argumentuma (pl. php artisan cleanup:resource Product)
    protected $signature = 'my_cleanup:resource {name}';

    // A parancs rövid leírása
    protected $description = 'Törli a táblát és az összes hozzá tartozó fájlt (modell, controller, request, stb.) egy adott erőforráshoz.';

    public function handle()
    {
        $name = ucfirst($this->argument('name')); // Erőforrás neve (pl. Product)
        if (!$this->confirm("Biztosan törölni akarod az összes fájlt és a(z) '{$name}' táblát?")) {
            $this->info('Művelet megszakítva.');
            return self::FAILURE;
        }
        $lowerName = strtolower($name); // Kisbetűs változat (pl. product)

        // 1. A tábla törlése (migráció visszavonása)
        // A migrációs fájlt a fájlrendszerben is meg kell keresni, mivel az időbélyeg miatt változik a neve.
        // Minta: Keresd meg az összes migrációs fájlt, ami a 'lowerName's-re illeszkedik.
        $searchPattern = database_path("migrations/*_{$lowerName}s_*.php");
        $migrationFiles = glob($searchPattern);

        if (!empty($migrationFiles)) {
            // 1. Rendezés fordított sorrendben
            // Mivel az időbélyeg a fájlnév elején van, a sztringek fordított rendezése 
            // biztosítja, hogy a legújabb fájlok (legnagyobb időbélyegek) kerüljenek előre.
            rsort($migrationFiles);

            $this->comment("Talált migrációs fájlok száma a(z) '{$lowerName}' erőforráshoz: " . \count($migrationFiles));

            // Létrehozzuk a kimeneti puffert, hogy a rollback üzeneteit felfogjuk.
            $outputBuffer = new BufferedOutput();
            // 2. Végigiterálás a fordított sorrendben rendezett tömbön
            foreach ($migrationFiles as $filePath) {
                $fileName = basename($filePath);

                $this->line("Rollback futtatása a(z) {$fileName} fájlra...");

                // Futtatjuk a rollback-et a migráció visszavonásához
                Artisan::call('migrate:rollback', [
                    // A rollbacket az egyedi fájlra kényszerítjük a --path segítségével
                    '--path' => 'database/migrations/' . $fileName,
                    '--force' => true
                ], $outputBuffer);

                $this->info("A(z) {$fileName} migráció visszavonva.");

                if (file_exists($filePath)) {
                    // A fájl fizikai törlése a lemezről
                    $this->line("Törlöm a(z) {$fileName} migrációs fájlt...");

                    // A tényleges törlés
                    unlink($filePath);

                    $this->info("A(z) {$fileName} fájl sikeresen törölve.");
                } else {
                    $this->warn("FIGYELEM: A(z) {$fileName} fájl nem található a törléshez, lehet, hogy már törölték.");
                }
            }

            $this->info("A tábla ({$lowerName}s) összes kapcsolódó migrációja sikeresen visszavonva, a helyes sorrendben.");
        } else {
            $this->warn("A migrációs fájlok nem találhatók a(z) '{$lowerName}' erőforráshoz a következő minta alapján: {$searchPattern}");
            $this->warn("Folytatom a többi fájl kézi törlését.");
        }

        // 2. Fájlok listája
        $filesToDelete = [
            // A glob() eredményét vesszük alapul a pontos migrációs fájl névhez
            !empty($migrationFile) ? $migrationFile[0] : null,
            // Egyéb fájlok
            app_path("Models/{$name}.php"),
            database_path("seeders/{$name}Seeder.php"),
            database_path("factories/{$name}Factory.php"),
            app_path("Http/Controllers/{$name}Controller.php"),
            app_path("Http/Requests/Store{$name}Request.php"),
            app_path("Http/Requests/Update{$name}Request.php"),
            app_path("Policies/{$name}Policy.php"),
            // Lehetnek még: Resource, Test-ek, View-k, stb.
            // API/Web Resource
            app_path("Http/Resources/{$name}Resource.php"),
            // Teszt fájlok
            base_path("tests/Feature/{$name}Test.php"),
            base_path("tests/Unit/{$name}Test.php"),
        ];

        // 3. Fájlok törlése
        $deletedCount = 0;
        foreach (array_filter($filesToDelete) as $file) {
            if (File::exists($file)) {
                if (File::delete($file)) {
                    $this->info("Sikeresen törölve: " . basename($file));
                    $deletedCount++;
                } else {
                    $this->error("Hiba történt a törlés során: " . basename($file));
                }
            } else {
                $this->warn("A fájl nem található: " . basename($file));
            }
        }

        if ($deletedCount > 0) {
            $this->comment("\n✅ Tisztítás befejezve a(z) '{$name}' erőforrásra. Törölt fájlok száma: {$deletedCount}");
        } else {
            $this->warn("\n⚠️ Nincs törlendő fájl a(z) '{$name}' erőforráshoz.");
        }


        // Utólagos javaslatok
        $this->newLine();
        $this->warn('--- Kézi tisztítás szükséges! ---');
        $this->warn("1. Távolítsd el a '{$lowerName}' útvonalat a routes/api.php fájlból.");
        $this->warn("2. database/seeders/DatabaseSeeder.php beli Seeder hivatkozás, tábla törlő parancs kiszedése");
        $this->warn("3. Ha használtál Policy-t, távolítsd el a Policy regisztrációt az app\Providers\AuthServiceProvider.php fájlból.");
        return self::SUCCESS;
    }
}
