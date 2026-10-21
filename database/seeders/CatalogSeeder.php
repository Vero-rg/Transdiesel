<?php

namespace Database\Seeders;

use App\Enums\Availability;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Seed the diesel injection catalog with representative data.
     */
    public function run(): void
    {
        $bombas = Category::query()->firstOrCreate(
            ['slug' => 'bombas'],
            ['name' => 'Bombas de diésel'],
        );

        $inyectores = Category::query()->firstOrCreate(
            ['slug' => 'inyectores'],
            ['name' => 'Inyectores de diésel'],
        );

        $brands = collect([
            'Amarok', 'Cummins', 'Ford', 'Caterpillar', 'Navistar', 'Bosch', 'Denso',
        ])->mapWithKeys(fn (string $name) => [
            $name => Brand::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true],
            ),
        ]);

        $products = [
            [
                'category' => $bombas,
                'brand' => 'Bosch',
                'sku' => 'TD-BOM-1001',
                'name' => 'Bomba de inyección Bosch VP44',
                'price' => 18900,
                'availability' => Availability::InStock,
                'description' => 'Bomba de inyección rotativa electrónica VP44 reconstruida y calibrada en banco de pruebas, ideal para motores diésel de 4 y 6 cilindros con gestión electrónica.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,600 bar',
                    'Tipo' => 'Rotativa electrónica',
                    'Compatibilidad' => 'Motores 4 y 6 cilindros',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $bombas,
                'brand' => 'Cummins',
                'sku' => 'TD-BOM-1002',
                'name' => 'Bomba de inyección Cummins P7100',
                'price' => 24500,
                'availability' => Availability::OnDemand,
                'description' => 'Bomba de inyección en línea P7100 para motores Cummins serie N14, totalmente probada en banco con entrega y presión verificadas.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,200 bar',
                    'Tipo' => 'En línea',
                    'Compatibilidad' => 'Cummins N14 / M11',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $bombas,
                'brand' => 'Ford',
                'sku' => 'TD-BOM-1003',
                'name' => 'Bomba rotativa Bosch VE Ford Ranger 2.5',
                'price' => 12300,
                'availability' => Availability::InStock,
                'description' => 'Bomba rotativa VE para Ford Ranger 2.5 TDI, reconstruida con refacciones originales y calibrada para entrega exacta de combustible.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 900 bar',
                    'Tipo' => 'Rotativa mecánica',
                    'Compatibilidad' => 'Ford Ranger 2.5 TDI',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $bombas,
                'brand' => 'Caterpillar',
                'sku' => 'TD-BOM-1004',
                'name' => 'Bomba de transferencia Caterpillar 320D',
                'price' => 6750,
                'availability' => Availability::InStock,
                'description' => 'Bomba de transferencia de combustible para excavadora Caterpillar 320D, con sellos y válvulas nuevas.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 300 bar',
                    'Tipo' => 'Transferencia',
                    'Compatibilidad' => 'Caterpillar 320D / C6.4',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $bombas,
                'brand' => 'Navistar',
                'sku' => 'TD-BOM-1005',
                'name' => 'Bomba de inyección Navistar DT466',
                'price' => 15800,
                'availability' => Availability::OutOfStock,
                'description' => 'Bomba de inyección para motor Navistar DT466, reconstruida y probada en banco con reporte de calibración incluido.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,400 bar',
                    'Tipo' => 'En línea',
                    'Compatibilidad' => 'Navistar DT466 / DT530',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $bombas,
                'brand' => 'Amarok',
                'sku' => 'TD-BOM-1006',
                'name' => 'Bomba de inyección Amarok 2.0 TDI',
                'price' => 9450,
                'availability' => Availability::InStock,
                'description' => 'Bomba de alta presión para Volkswagen Amarok 2.0 TDI, verificada en banco con prueba de estanqueidad y caudal.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,800 bar',
                    'Tipo' => 'Alta presión common rail',
                    'Compatibilidad' => 'Amarok 2.0 TDI',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $bombas,
                'brand' => 'Denso',
                'sku' => 'TD-BOM-1007',
                'name' => 'Bomba de alta presión Denso Common Rail',
                'price' => 21700,
                'availability' => Availability::OnDemand,
                'description' => 'Bomba de alta presión Denso para sistemas common rail, con certificado de calibración y probada a presión máxima.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 2,000 bar',
                    'Tipo' => 'Alta presión common rail',
                    'Compatibilidad' => 'Sistemas common rail Denso',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $inyectores,
                'brand' => 'Bosch',
                'sku' => 'TD-INY-2001',
                'name' => 'Inyector Bosch Common Rail 0445110',
                'price' => 4850,
                'availability' => Availability::InStock,
                'description' => 'Inyector common rail Bosch remanufacturado, con boquilla nueva y prueba de caudal por cilindro.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,800 bar',
                    'Tipo' => 'Electromagnético',
                    'Compatibilidad' => 'Motores 4 cilindros common rail',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $inyectores,
                'brand' => 'Denso',
                'sku' => 'TD-INY-2002',
                'name' => 'Inyector Denso Caterpillar C7',
                'price' => 5900,
                'availability' => Availability::InStock,
                'description' => 'Inyector Denso para motor Caterpillar C7, probado en banco con reporte de retorno y pulverización.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,900 bar',
                    'Tipo' => 'Common rail',
                    'Compatibilidad' => 'Caterpillar C7',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $inyectores,
                'brand' => 'Cummins',
                'sku' => 'TD-INY-2003',
                'name' => 'Inyector Cummins ISX 15',
                'price' => 7200,
                'availability' => Availability::OnDemand,
                'description' => 'Inyector de alta presión para Cummins ISX 15, reconstruido con componentes originales y calibrado electrónicamente.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 2,200 bar',
                    'Tipo' => 'Electrónico',
                    'Compatibilidad' => 'Cummins ISX 15',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $inyectores,
                'brand' => 'Ford',
                'sku' => 'TD-INY-2004',
                'name' => 'Inyector Ford Power Stroke 6.7L',
                'price' => 6150,
                'availability' => Availability::InStock,
                'description' => 'Inyector para Ford Power Stroke 6.7L, con prueba de estanqueidad y patrón de aspersión verificado.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 2,000 bar',
                    'Tipo' => 'Piezoeléctrico',
                    'Compatibilidad' => 'Ford Power Stroke 6.7L',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $inyectores,
                'brand' => 'Navistar',
                'sku' => 'TD-INY-2005',
                'name' => 'Inyector Navistar MaxxForce',
                'price' => 5400,
                'availability' => Availability::OutOfStock,
                'description' => 'Inyector para motor Navistar MaxxForce, reconstruido y probado en banco de inyección diésel.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,900 bar',
                    'Tipo' => 'Electrónico',
                    'Compatibilidad' => 'Navistar MaxxForce 11 / 13',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $inyectores,
                'brand' => 'Amarok',
                'sku' => 'TD-INY-2006',
                'name' => 'Inyector Amarok 2.0 TDI Piezoeléctrico',
                'price' => 4300,
                'availability' => Availability::InStock,
                'description' => 'Inyector piezoeléctrico para Volkswagen Amarok 2.0 TDI, verificado con prueba de retorno y codificación.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,800 bar',
                    'Tipo' => 'Piezoeléctrico',
                    'Compatibilidad' => 'Amarok 2.0 TDI',
                    'Garantía' => '6 meses',
                ],
            ],
            [
                'category' => $inyectores,
                'brand' => 'Caterpillar',
                'sku' => 'TD-INY-2007',
                'name' => 'Inyector Caterpillar 3126',
                'price' => 5600,
                'availability' => Availability::InStock,
                'description' => 'Inyector para motor Caterpillar 3126, remanufacturado con boquilla y aguja nuevas.',
                'specifications' => [
                    'Presión de trabajo' => 'Hasta 1,500 bar',
                    'Tipo' => 'Hidráulico mecánico',
                    'Compatibilidad' => 'Caterpillar 3126 / 3126B',
                    'Garantía' => '6 meses',
                ],
            ],
        ];

        foreach ($products as $data) {
            $brand = $brands[$data['brand']] ?? null;

            $product = Product::query()->updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => $data['category']->id,
                    'brand_id' => $brand?->id,
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'description' => $data['description'],
                    'specifications' => $data['specifications'],
                    'price' => $data['price'],
                    'availability' => $data['availability'],
                ],
            );

            if ($product->reviews()->doesntExist()) {
                $this->seedReviews($product);
            }
        }
    }

    private function seedReviews(Product $product): void
    {
        $samples = [
            ['author' => 'Ricardo Medina', 'rating' => 5, 'comment' => 'Excelente calidad, quedó perfecto en mi unidad y el envío fue muy rápido.'],
            ['author' => 'Laura Ortiz', 'rating' => 4, 'comment' => 'Buen producto, llegó bien embalado. El equipo me ayudó a confirmar la compatibilidad.'],
            ['author' => 'Miguel Ángel Ramírez', 'rating' => 5, 'comment' => 'Trabajo realmente especializado, la calibración viene impecable.'],
        ];

        foreach ($samples as $sample) {
            Review::query()->create([
                'product_id' => $product->id,
                'author_name' => $sample['author'],
                'author_email' => Str::slug($sample['author'], '.').'@example.com',
                'rating' => $sample['rating'],
                'comment' => $sample['comment'],
                'is_approved' => true,
            ]);
        }
    }
}
