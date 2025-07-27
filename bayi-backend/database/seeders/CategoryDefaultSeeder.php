<?php

namespace Database\Seeders;

use App\Models\Catalog\Category\Categories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->dataList() as $key => $value) {
            $category = Categories::create([
                'customer_id' => null,
                'is_default' => true,
                'parent_id' => 0,
            ]);
            $category->descriptions()->create([
                'name' => $value['tr'],
                'language' => 'tr',
            ]);
            $category->descriptions()->create([
                'name' => $value['en'],
                'language' => 'en',
            ]);

            $this->subCategory($value, $category->id);
        }
    }

    private function subCategory($data, $parent_id)
    {
        foreach ($data['sub'] as $key => $value) {
            $category = Categories::create([
                'customer_id' => null,
                'is_default' => true,
                'parent_id' => $parent_id,
            ]);
            $category->descriptions()->create([
                'name' => $value['tr'],
                'language' => 'tr',
            ]);
            $category->descriptions()->create([
                'name' => $value['en'],
                'language' => 'en',
            ]);

            if (isset($value['sub'])) {
                $this->subCategory($value, $category->id);
            }
        }
    }

    private function dataList()
    {
        $mobilya = array(
            [
                "tr" => "Mobilya",
                "en" => "Furniture",
                "sub" => [
                    array(
                        "tr" => "Oturma Odası Mobilyaları",
                        "en" => "Living Room Furniture",
                        "sub" => [
                            [
                                "tr" => "Koltuklar & Kanepe",
                                "en" => "Sofas & Couches",
                            ],
                            array(
                                "tr" => "Kanepeler",
                                "en" => "Loveseats"
                            ),
                            array(
                                "tr" => "Köşe Koltuk Takımları",
                                "en" => "Sectionals"
                            ), array(
                                "tr" => "Koltuk & Dinlenme Koltukları",
                                "en" => "Recliners & Lounge Chairs"
                            ), array(
                                "tr" => "Sehpa & Yemek Masası",
                                "en" => "Coffee & End Tables"
                            ), array(
                                "tr" => "TV Üniteleri & Eğlence Merkezleri",
                                "en" => "TV Stands & Entertainment Centers"
                            ), array(
                                "tr" => "Kitaplık & Raf Üniteleri",
                                "en" => "Bookcases & Shelving Units"
                            )
                        ]
                    ),
                    array(
                        "tr" => "Yatak Odası Mobilyaları",
                        "en" => "Bedroom Furniture",
                        "sub" => [
                            array(
                                "tr" => "Yatak & Yatak Çerçeveleri",
                                "en" => "Beds & Bed Frames"
                            ),
                            array(
                                "tr" => "Yatak",
                                "en" => "Mattresses"
                            ),
                            array(
                                "tr" => "Komodinler",
                                "en" => "Nightstands"
                            ),
                            array(
                                "tr" => "Şifonyer & Göğüsler",
                                "en" => "Dressers & Chests"
                            ),
                            array(
                                "tr" => "Gardırop & Elbiselikler",
                                "en" => "Wardrobes & Armoires"
                            ),
                            array(
                                "tr" => "Yatak Odası Takımları",
                                "en" => "Bedroom Sets"
                            )
                        ]
                    ),
                    array(
                        "tr" => "Yemek Odası Mobilyaları",
                        "en" => "Dining Room Furniture",
                        "sub" => [
                            array(
                                "tr" => "Yemek Masaları",
                                "en" => "Dining Tables"
                            ), array(
                                "tr" => "Yemek Sandalyeleri",
                                "en" => "Dining Chairs"
                            ), array(
                                "tr" => "Bar Tabureleri",
                                "en" => "Bar Stools"
                            ), array(
                                "tr" => "Bufe & Şifonyer",
                                "en" => "Buffets & Sideboards"
                            ), array(
                                "tr" => "Vitrinler",
                                "en" => "China Cabinets"
                            ), array(
                                "tr" => "Yemek Odası Takımları",
                                "en" => "Dining Room Sets"
                            )
                        ]
                    ),
                    array(
                        "tr" => "Ofis Mobilyaları",
                        "en" => "Office Furniture",
                        "sub" => [
                            array(
                                "tr" => "Masalar",
                                "en" => "Desks"
                            ), array(
                                "tr" => "Ofis Koltukları",
                                "en" => "Office Chairs"
                            ), array(
                                "tr" => "Dosya Dolapları",
                                "en" => "File Cabinets"
                            ), array(
                                "tr" => "Kitaplık",
                                "en" => "Bookcases"
                            ), array(
                                "tr" => "Toplantı Masaları",
                                "en" => "Conference Tables"
                            ), array(
                                "tr" => "Ofis Depolama",
                                "en" => "Office Storage"
                            )
                        ]
                    ),
                    array(
                        "tr" => "Dış Mekan Mobilyaları",
                        "en" => "Outdoor Furniture",
                        "sub" => [
                            array(
                                "tr" => "Patio Yemek Takımları",
                                "en" => "Patio Dining Sets"
                            ), array(
                                "tr" => "Dış Mekan Oturma Grupları & Koltuklar",
                                "en" => "Outdoor Seating & Sofas"
                            ), array(
                                "tr" => "Dış Mekan Sehpa & Yan Sehpa",
                                "en" => "Outdoor Coffee & Side Tables"
                            ), array(
                                "tr" => "Dış Mekan Şezlonglar & Gündüz Yatakları",
                                "en" => "Outdoor Loungers & Daybeds"
                            ), array(
                                "tr" => "Hamaklar & Salıncak Koltuklar",
                                "en" => "Hammocks & Swing Chairs"
                            ), array(
                                "tr" => "Dış Mekan Şemsiyeler & Gölge",
                                "en" => "Outdoor Umbrellas & Shades"
                            )
                        ]
                    ),
                ]
            ],
            //home decor
            array(
                "tr" => "Ev Dekorasyonu",
                "en" => "Home Decor",
                "sub" => [
                    array(
                        "tr" => "Aydınlatma",
                        "en" => "Lighting",
                        "sub" => [
                            array(
                                "tr" => "Masa Lambaları",
                                "en" => "Table Lamps"
                            ), array(
                                "tr" => "Yer Lambaları",
                                "en" => "Floor Lamps"
                            ), array(
                                "tr" => "Avize Lambaları",
                                "en" => "Pendant Lights"
                            ), array(
                                "tr" => "Avizeler",
                                "en" => "Chandeliers"
                            ), array(
                                "tr" => "Duvar Lambaları",
                                "en" => "Wall Lights"
                            )
                        ]
                    ),
                    array(
                        "tr" => "Halı & Kilimler",
                        "en" => "Rugs & Carpets",
                        "sub" => [
                            array(
                                "tr" => "Bölge Halıları",
                                "en" => "Area Rugs"
                            ),
                            array(
                                "tr" => "Koşucular",
                                "en" => "Runners"
                            ),
                            array(
                                "tr" => "Kapı Halıları",
                                "en" => "Door Mats"
                            ),
                            array(
                                "tr" => "Dış Mekan Halıları",
                                "en" => "Outdoor Rugs"
                            )
                        ]
                    ),
                    array(
                        "tr" => "Duvar Dekorasyonu",
                        "en" => "Wall Decor",
                        "sub" => [
                            array(
                                "tr" => "Tablolar",
                                "en" => "Paintings"
                            ),
                            array(
                                "tr" => "Baskılar",
                                "en" => "Prints"
                            ),
                            array(
                                "tr" => "Duvar Saatleri",
                                "en" => "Wall Clocks"
                            ),
                            array(
                                "tr" => "Duvar Aynaları",
                                "en" => "Wall Mirrors"
                            ),
                            array(
                                "tr" => "Duvar Rafları",
                                "en" => "Wall Shelves"
                            )
                        ]
                    )
                ],

            ),
            //tekstil
            array(
                "tr" => "Tekstil ve Giyim",
                "en" => "Textiles & Apparel",
                "sub" => [
                    array(
                        "tr" => "Ev Tekstili",
                        "en" => "Home Textiles",
                        "sub" => [
                            array(
                                "tr" => "Yatak Odası Tekstili",
                                "en" => "Bedroom Textiles",
                                "sub" => [
                                    array(
                                        "tr" => "Yatak Çarşafları",
                                        "en" => "Bed Sheets"
                                    ),
                                    array(
                                        "tr" => "Yorgan Kılıfları",
                                        "en" => "Duvet Covers"
                                    ),
                                    array(
                                        "tr" => "Yastık Kılıfları",
                                        "en" => "Pillow Cases"
                                    ),
                                    array(
                                        "tr" => "Battaniye & Yorgan",
                                        "en" => "Quilts & Comforters"
                                    ),
                                    array(
                                        "tr" => "Yatak Örtüleri",
                                        "en" => "Bedspreads"
                                    )
                                ]
                            ), array(
                                "tr" => "Banyo Tekstili",
                                "en" => "Bathroom Textiles",
                                "Towels" => array(
                                    "tr" => "Havlular",
                                    "en" => "Towels"
                                ),
                                "Bathrobes" => array(
                                    "tr" => "Bornozlar",
                                    "en" => "Bathrobes"
                                ),
                                "Shower Curtains" => array(
                                    "tr" => "Duş Perdeleri",
                                    "en" => "Shower Curtains"
                                ),
                                "Bath Mats" => array(
                                    "tr" => "Banyo Paspası",
                                    "en" => "Bath Mats"
                                )
                            ), array(
                                "tr" => "Mutfak Tekstili",
                                "en" => "Kitchen Textiles",
                                "Tablecloths" => array(
                                    "tr" => "Masa Örtüleri",
                                    "en" => "Tablecloths"
                                ),
                                "Napkins" => array(
                                    "tr" => "Peçeteler",
                                    "en" => "Napkins"
                                ),
                                "Kitchen Towels" => array(
                                    "tr" => "Mutfak Havluları",
                                    "en" => "Kitchen Towels"
                                ),
                                "Aprons" => array(
                                    "tr" => "Önlükler",
                                    "en" => "Aprons"
                                ),
                                "Oven Mitts & Pot Holders" => array(
                                    "tr" => "Fırın Eldivenleri & Tencere Tutacakları",
                                    "en" => "Oven Mitts & Pot Holders"
                                )
                            )
                        ]
                    ),
                    array(
                        "tr" => "Giyim",
                        "en" => "Apparel",
                        "sub" => [
                            array(
                                "tr" => "Kadın Giyim",
                                "en" => "Women's Apparel",
                                "sub" => [
                                    array(
                                        "tr" => "Elbiseler",
                                        "en" => "Dresses"
                                    ),
                                    array(
                                        "tr" => "Üst Giysiler & Bluzlar",
                                        "en" => "Tops & Blouses"
                                    ),
                                    array(
                                        "tr" => "Etekler & Pantolonlar",
                                        "en" => "Skirts & Pants"
                                    ),
                                    array(
                                        "tr" => "Ceketler & Montlar",
                                        "en" => "Jackets & Coats"
                                    ),
                                    array(
                                        "tr" => "İç Çamaşırı & Pijama Takımları",
                                        "en" => "Lingerie & Sleepwear"
                                    )
                                ]
                            ),
                            array(
                                "tr" => "Erkek Giyim",
                                "en" => "Men's Apparel",
                                "sub" => [
                                    array(
                                        "tr" => "Gömlekler",
                                        "en" => "Shirts"
                                    ),
                                    array(
                                        "tr" => "Pantolonlar & Kotlar",
                                        "en" => "Trousers & Jeans"
                                    ),
                                    array(
                                        "tr" => "Takımlar & Ceketler",
                                        "en" => "Suits & Blazers"
                                    ),
                                    array(
                                        "tr" => "Ceketler & Montlar",
                                        "en" => "Coats & Jackets"
                                    ),
                                    array(
                                        "tr" => "İç Çamaşırı & Çoraplar",
                                        "en" => "Underwear & Socks"
                                    )
                                ]
                            ),
                            array(
                                "tr" => "Çocuk Giyim",
                                "en" => "Children's Apparel",
                                "sub" => [
                                    array(
                                        "tr" => "Bebek Giysileri",
                                        "en" => "Baby Clothes"
                                    ),
                                    array(
                                        "tr" => "Erkek Çocuk Giysileri",
                                        "en" => "Boys' Clothes"
                                    ),
                                    array(
                                        "tr" => "Kız Çocuk Giysileri",
                                        "en" => "Girls' Clothes"
                                    ),
                                    array(
                                        "tr" => "Çocuk Giysi Dış Giysileri",
                                        "en" => "Children's Outerwear"
                                    ),
                                    array(
                                        "tr" => "Çocuk Pijama Takımları",
                                        "en" => "Children's Sleepwear"
                                    )
                                ]
                            )
                        ]
                    ),
                    array(
                        "tr" => "Teknik Tekstiller",
                        "en" => "Technical Textiles",
                        "sub" => [
                            array(
                                "tr" => "Otomotiv Tekstilleri",
                                "en" => "Automotive Textiles",
                                "sub" => [
                                    array(
                                        "tr" => "Koltuk Kılıfları",
                                        "en" => "Seat Covers"
                                    ),
                                    array(
                                        "tr" => "Hava Yastığı Kumaşları",
                                        "en" => "Airbag Fabrics"
                                    ),
                                    array(
                                        "tr" => "Otomotiv Halıları",
                                        "en" => "Automotive Carpets"
                                    )
                                ]
                            ),
                            array(
                                "tr" => "Tıbbi Tekstiller",
                                "en" => "Medical Textiles",
                                "sub" => [
                                    array(
                                        "tr" => "Cerrahi Elbiseler",
                                        "en" => "Surgical Gowns"
                                    ),
                                    array(
                                        "tr" => "Yüz Maskeleri",
                                        "en" => "Face Masks"
                                    ),
                                    array(
                                        "tr" => "Bantlar & Yara Örtüleri",
                                        "en" => "Bandages & Dressings"
                                    )
                                ]
                            ),
                            array(
                                "tr" => "İnşaat Tekstilleri",
                                "en" => "Construction Textiles",
                                "sub" => [
                                    array(
                                        "tr" => "Jeotekstiller",
                                        "en" => "Geotextiles"
                                    ),
                                    array(
                                        "tr" => "Brandalar",
                                        "en" => "Tarpaulins"
                                    ),
                                    array(
                                        "tr" => "İskele Ağları",
                                        "en" => "Scaffolding Nets"
                                    )
                                ]
                            )
                        ]
                    )
                ]
            )
        );

        return $mobilya;
    }
}
