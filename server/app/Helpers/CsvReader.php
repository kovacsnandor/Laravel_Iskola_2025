<?php
//Névtér: Ennek a segítségével fogjuk elérni
namespace App\Helpers;

use Illuminate\Support\Facades\File; // Ha a File Facade-et akarod használni a natív PHP helyett

class CsvReader
{
    /**
     * Beolvas egy CSV fájlt az adatbázis útvonalról és asszociatív tömbként adja vissza.
     * * @param string $fileName Az elérési út a database_path() után (pl. 'csv/products.csv').
     * @param string $delimiter Az oszlopelválasztó (alapértelmezett: ';').
     * @return array Ha nincs ilyen fájl, akkor üres tömbbel tér vissza
     */
    public static function csvToArray(string $fileName, string $delimiter = ';'): array
    {
        $filePath = database_path(path: $fileName);
        $data = [];

        if (!File::exists($filePath)) {
            return $data;
        }

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle, 0, $delimiter);

            while (($cols = fgetcsv($handle, 0, $delimiter)) !== false) {
                if ($header && count($header) === count($cols)) {
                    $data[] = array_combine($header, $cols);
                }
            }
            
            fclose($handle);
        }

        return $data;
    }
}