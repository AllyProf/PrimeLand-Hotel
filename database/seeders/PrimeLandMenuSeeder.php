<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Recipe;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PrimeLandMenuSeeder extends Seeder
{
    /**
     * Seed the full PrimeLand Hotel Food & Drinks Menu.
     *
     * Food items → recipes table (used by Waiter POS)
     * Drinks     → products + product_variants tables (bar stock)
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->seedFood();
            $this->seedDrinks();

            DB::commit();
            $this->command->info('✅ PrimeLand Menu seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error seeding menu: ' . $e->getMessage());
            throw $e;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  FOOD  (Recipes table)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedFood(): void
    {
        // [name, category, tsh_price, usd_price, prep_min, description]
        $foodItems = [

            // ── SALADS ────────────────────────────────────────────────────
            ['Caesar Salad',                      'salads',      12000,  6, 10,
                'Fresh lettuce, grilled chicken, boiled eggs, cheddar cheese, croutons, and Caesar dressing.'],
            ['Garden Salad',                      'salads',      15000,  7, 10,
                'Mixed herbs, lettuce, tomatoes, cucumbers, onions, and peppers with honey mustard dressing.'],
            ['Chicken Salad',                     'salads',      15000,  7, 10,
                'Grilled chicken, lettuce, mustard dressing, avocado, cucumber, and tomatoes.'],
            ['Butternut Salad',                   'salads',      15000,  7, 10,
                'Lettuce, tomatoes, cucumber, grilled butternut, feta cheese, butternut seeds & honey mustard dressing.'],
            ['Thai Beef Salad',                   'salads',      20000,  9, 15,
                'Beef fillet strips, lettuce, cucumber, tomatoes, red cabbage, sesame seeds & Thai-Italian dressing.'],
            ['Fruit Salad',                       'salads',       5000,  3,  5,
                'Mixed seasonal fresh fruits.'],
            ['Mixed Fruit Salad in Custard Sauce','salads',      10000,  5,  8,
                'Seasonal fruits served with sweet custard sauce.'],

            // ── SOUPS ─────────────────────────────────────────────────────
            ['Banana Soup (Mtori)',               'soups',        8000,  4, 20,
                'Traditional Tanzanian soup made of banana, potatoes, and beef.'],
            ['Roasted Peanut Soup',               'soups',       10000,  5, 20,
                'Creamy roasted peanut soup served with bread and butter.'],
            ['Butternut Squash Soup',             'soups',       10000,  5, 20,
                'Butternut and onion soup served with bread & butter.'],
            ['Vegetable Soup',                    'soups',        6000,  3, 15,
                'Seasonal vegetables including carrots, onions, and green peppers.'],

            // ── SNACKS & BITES ────────────────────────────────────────────
            ['Beef Samosa',                       'snacks',      12000,  4, 10,
                'Mild spiced beef samosa served with sweet chili dip and salad.'],
            ['Chicken Wings',                     'snacks',      15000,  7, 20,
                'Fried chicken wings served with fries, sweet & sour dip, and salad.'],
            ['Chicken Nuggets',                   'snacks',      15000,  7, 15,
                'Crunchy chicken cubes marinated with ginger, lemon, garlic & breadcrumbs. Served with chips and sauce.'],
            ['Chicken Spring Rolls',              'snacks',      12000,  4, 15,
                'Served with sweet chili dip and salad.'],
            ['Vegetable Spring Rolls',            'snacks',       6000,  3, 15,
                'Vegetarian rolls served with sweet chili dip.'],
            ['Popcorn',                           'snacks',       2000,  1,  5,
                'Freshly prepared salted or buttered popcorn.'],

            // ── BEEF ──────────────────────────────────────────────────────
            ['Beef Curry',                        'main_course', 15000,  7, 25,
                'Swahili-style mild spiced curry with onions, carrots, potatoes, bell peppers & coconut cream.'],
            ['Beef Stir Fry',                     'main_course', 20000,  9, 20,
                'Beef stir-fried with seasonal vegetables, soy sauce, oyster sauce, garlic, ginger & fresh chili.'],
            ['Pepper Steak',                      'main_course', 25000, 11, 25,
                'Beef fillet marinated with black pepper, soy sauce & ginger, served with cream pepper sauce and salad.'],
            ['Grilled Beef Steak',                'main_course', 25000, 11, 30,
                'Marinated in black pepper, soy sauce, oyster sauce & garlic. Served with chef\'s salad and BBQ/tomato sauce.'],
            ['Beef Mchemsho',                     'main_course', 20000,  9, 35,
                'Boiled beef served with mixed vegetables and Irish potatoes or green banana.'],
            ['Beef Noodles',                      'main_course', 18000,  7, 20,
                'Noodles cooked with beef and oyster sauce seasoning.'],

            // ── GOAT & PORK ───────────────────────────────────────────────
            ['Mbuzi Kitunguu',                    'main_course', 18000,  8, 30,
                'Fried goat with onions, soy sauce & mama sita sauce.'],
            ['Pork Stir Fry',                     'main_course', 18000,  8, 20,
                'Stir-fried pork with mixed vegetables, soy sauce, garlic & ginger.'],
            ['Pork Chops',                        'main_course', 18000,  8, 25,
                'Grilled pork chops served with honey mustard sauce and chef\'s salad.'],

            // ── CHICKEN ───────────────────────────────────────────────────
            ['Fried Chicken',                     'main_course', 15000,  7, 20,
                'Crispy fried chicken served with chips and coleslaw salad.'],
            ['Chicken Stir Fry',                  'main_course', 15000,  7, 20,
                'Boneless chicken stir-fried with mixed vegetables and fresh chili.'],
            ['Chicken Makange',                   'main_course', 18000,  8, 25,
                'Fried chicken with carrots, peppers, spices & tomato sauce, served with salad.'],
            ['Local Chicken Mchemsho',            'main_course', 20000,  9, 40,
                'Boiled local chicken served with mixed vegetables and banana.'],
            ['Grilled Chicken Breast',            'main_course', 15000,  7, 25,
                'Boneless grilled chicken breast served with chef\'s salad.'],

            // ── FISH ──────────────────────────────────────────────────────
            ['Grilled Fish Fillet (Nile Perch)',  'main_course', 25000, 11, 20,
                'Grilled Nile perch fillet served with salad and tartar sauce.'],
            ['Fried Fish Fillet (Nile Perch)',    'main_course', 25000, 11, 20,
                'Fried Nile perch fillet served with salad and tartar sauce.'],
            ['Fish Fingers',                      'main_course', 18000,  7, 15,
                'Nile perch fillet coated in golden spiced breadcrumbs served with fries and salad.'],
            ['Fish Makange',                      'main_course', 20000,  9, 25,
                'Fried fish with mixed vegetables and spices.'],
            ['Fried Fish (Whole Tilapia)',         'main_course', 25000, 11, 25,
                'Whole tilapia fried and served with salad.'],
            ['Boiled Fish Tilapia (Mchemsho)',     'main_course', 25000, 11, 35,
                'Whole tilapia boiled with vegetables and potatoes.'],

            // ── PASTA ─────────────────────────────────────────────────────
            ['Pasta Pesto',                       'main_course', 20000,  9, 20,
                'Macaroni pasta with green pesto sauce.'],
            ['Cream Pasta',                       'main_course', 25000, 11, 20,
                'Pasta with cream, spinach and cheese.'],
            ['Spaghetti Carbonara',               'main_course', 20000,  9, 20,
                'Pasta with cured pork, cream and hard cheese.'],
            ['Spaghetti Bolognese',               'main_course', 18000,  7, 20,
                'Spaghetti with minced beef and tomato sauce.'],

            // ── BURGERS (all served with fries & chef's salad) ───────────
            ['Beef Burger',                       'main_course', 18000,  8, 15,
                'Beef patty burger served with French fries & chef\'s salad. Toppings: Pineapple, Avocado, Chicken, Beef, Bacon.'],
            ['Veggie Burger',                     'main_course', 13000,  6, 15,
                'Vegetarian burger served with French fries & chef\'s salad.'],
            ['Classic Chicken Burger',            'main_course', 15000,  7, 15,
                'Chicken burger served with French fries & chef\'s salad. Add toppings available.'],

            // ── PIZZA ─────────────────────────────────────────────────────
            ['Beef Pizza',                        'main_course', 18000,  8, 25,
                'Fresh dough with mozzarella cheese, tomato sauce and seasoned beef.'],
            ['Chicken Pizza',                     'main_course', 18000,  8, 25,
                'Fresh dough with mozzarella cheese, tomato sauce and grilled chicken.'],
            ['Hawaiian Pizza',                    'main_course', 15000,  7, 25,
                'Fresh dough with mozzarella, tomato sauce, ham and pineapple.'],
            ['Margarita Pizza',                   'main_course', 13000,  6, 20,
                'Classic fresh dough with mozzarella cheese and tomato sauce.'],
            ['Vegetable Pizza',                   'main_course', 12000,  6, 20,
                'Fresh dough with mozzarella, tomato sauce and seasonal vegetables.'],
            ['Primeland Special Pizza',           'main_course', 25000, 11, 30,
                'Our signature pizza loaded with premium toppings — chef\'s daily special creation.'],

            // ── SANDWICHES & SHAWARMA ─────────────────────────────────────
            ['Beef Sandwich',                     'snacks',      10000,  5, 10,
                'Tender beef in toasted bread with fresh salad and house sauce.'],
            ['Chicken Sandwich',                  'snacks',      15000,  6, 10,
                'Grilled chicken in toasted bread with fresh salad and sauce.'],
            ['Vegetable Sandwich',                'snacks',      15000,  7, 10,
                'Fresh seasonal vegetables in toasted bread with house dressing.'],
            ['Chicken Shawarma',                  'snacks',      12000,  6, 15,
                'Spiced chicken wrapped in flatbread with garlic sauce and fresh vegetables.'],

            // ── DESSERTS ──────────────────────────────────────────────────
            ['Chocolate Ice Cream',               'desserts',     7000,  3,  5,
                'Rich creamy chocolate ice cream.'],
            ['Vanilla Ice Cream',                 'desserts',     7000,  3,  5,
                'Classic creamy vanilla ice cream.'],
            ['Strawberry Ice Cream',              'desserts',     7000,  3,  5,
                'Sweet strawberry flavoured ice cream.'],
            ['Cake of the Day',                   'desserts',    10000,  5,  5,
                'Ask your waiter for today\'s freshly baked cake selection.'],

            // ── SIDES & TRADITIONAL (Part of Main Course logic) ──────────
            ['Ugali',                             'main_course',  2000,  1,  5,  'Traditional Tanzanian staple made of maize flour.'],
            ['Plain Rice',                        'main_course',  3000,  1.5,5,  'Steamed white rice.'],
            ['French Fries',                      'main_course',  4000,  2,  8,  'Golden crispy French fries.'],
            ['Potato Wedges',                     'main_course',  4000,  2,  8,  'Seasoned oven-baked potato wedges.'],
            ['Mashed Potatoes',                   'main_course',  4000,  2, 10,  'Creamy butter mashed potatoes.'],
            ['Fried Banana',                      'main_course',  3000,  1.5,8,  'Sweet fried ripe banana — a local favourite.'],
        ];

        foreach ($foodItems as [$name, $category, $tsh, $usd, $prep, $desc]) {
            Recipe::updateOrCreate(
                ['name' => $name],
                [
                    'category'          => $category,
                    'selling_price'     => $tsh,
                    'selling_price_usd' => $usd,
                    'prep_time'         => $prep,
                    'description'       => $desc,
                    'is_available'      => true,
                ]
            );
        }

        $this->command->info('  → Food menu seeded (' . count($foodItems) . ' items)');
    }


    // ─────────────────────────────────────────────────────────────────────────
    //  DRINKS  (Products + ProductVariants table)
    // ─────────────────────────────────────────────────────────────────────────
    private function seedDrinks(): void
    {
        /*
         * Format per brand entry:
         *   brandName => [category, type, variants[]]
         *
         * Each variant:
         *   [name, size, sellMethod (pic|glass|mixed), servings, pricePerServing, pricePerServingUSD, pricePerPic, pricePerPicUSD]
         */
        $brands = [

            // ── LOCAL BEERS (Tsh 3,000 / $2) ────────────────────────────────
            'Serengeti Lite'       => ['alcoholic_beverage', 'drink', [
                ['Standard', '500 ml', 'pic', 1, 0, 0, 3000, 2],
            ]],
            'Serengeti Lager'      => ['alcoholic_beverage', 'drink', [
                ['Standard', '500 ml', 'pic', 1, 0, 0, 3000, 2],
            ]],
            'Safari Lager'         => ['alcoholic_beverage', 'drink', [
                ['Standard', '500 ml', 'pic', 1, 0, 0, 3000, 2],
            ]],
            'Kilimanjaro Lager'    => ['alcoholic_beverage', 'drink', [
                ['Standard', '500 ml', 'pic', 1, 0, 0, 3000, 2],
            ]],
            'Castle Lite'          => ['alcoholic_beverage', 'drink', [
                ['Standard', '500 ml', 'pic', 1, 0, 0, 3000, 2],
            ]],

            // ── IMPORTED BEERS (Tsh 5,000 / $3) ─────────────────────────────
            'Heineken'             => ['alcoholic_beverage', 'drink', [
                ['Standard', '330 ml', 'pic', 1, 0, 0, 5000, 3],
            ]],
            'Windhoek'             => ['alcoholic_beverage', 'drink', [
                ['Standard', '330 ml', 'pic', 1, 0, 0, 5000, 3],
            ]],
            'Flying Fish'          => ['alcoholic_beverage', 'drink', [
                ['Standard', '330 ml', 'pic', 1, 0, 0, 5000, 3],
            ]],
            'Savannah'             => ['alcoholic_beverage', 'drink', [
                ['Dry', '330 ml', 'pic', 1, 0, 0, 5000, 3],
            ]],
            'Desperados'           => ['alcoholic_beverage', 'drink', [
                ['Standard', '330 ml', 'pic', 1, 0, 0, 5000, 3],
            ]],
            'Smirnoff Ice'         => ['alcoholic_beverage', 'drink', [
                ['Standard', '330 ml', 'pic', 1, 0, 0, 5000, 3],
            ]],
            'Brutal Fruit'         => ['alcoholic_beverage', 'drink', [
                ['Standard', '330 ml', 'pic', 1, 0, 0, 7000, 3],
            ]],

            // ── WINES ────────────────────────────────────────────────────────
            'Drostdy Hof'          => ['wines', 'drink', [
                ['Red',    '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
                ['White',  '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
            ]],
            'Four Cousins'         => ['wines', 'drink', [
                ['Sweet Red',  '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
                ['Sweet White','750 ml', 'mixed', 5, 7000, 3, 30000, 15],
            ]],
            'Robertson'            => ['wines', 'drink', [
                ['Sweet Red',  '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
                ['Sweet White','750 ml', 'mixed', 5, 7000, 3, 30000, 15],
            ]],
            'Dompo'                => ['wines', 'drink', [
                ['Red', '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
            ]],
            'Pearly Bay'           => ['wines', 'drink', [
                ['Red',   '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
                ['White', '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
            ]],
            'Alta Wine'            => ['wines', 'drink', [
                ['Red',   '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
                ['White', '750 ml', 'mixed', 5, 7000, 3, 30000, 15],
            ]],
            'KWV'                  => ['wines', 'drink', [
                ['Red',   '750 ml', 'mixed', 5, 7000, 3, 40000, 20],
                ['White', '750 ml', 'mixed', 5, 7000, 3, 40000, 20],
            ]],
            'Martin Rose'          => ['wines', 'drink', [
                ['Rosé', '750 ml', 'mixed', 5, 7000, 3, 35000, 18],
            ]],

            // ── GIN ──────────────────────────────────────────────────────────
            'Kvant Gin'            => ['spirits', 'drink', [
                ['Standard', '750 ml', 'mixed', 25, 3000, 1.5, 30000, 15],
                ['Pocket',   '200 ml', 'mixed', 6,  3000, 1.5, 12000, 6],
            ]],
            'Konyagi Gin'          => ['spirits', 'drink', [
                ['Standard', '750 ml', 'mixed', 25, 2000, 1, 20000, 10],
            ]],
            'Gordons Gin'          => ['spirits', 'drink', [
                ['Standard', '750 ml', 'mixed', 25, 4000, 2, 80000, 35],
            ]],

            // ── VODKA ─────────────────────────────────────────────────────────
            'Smirnoff Vodka'       => ['spirits', 'drink', [
                ['Red Label', '750 ml', 'mixed', 25, 3000, 1.5, 40000, 20],
            ]],
            'Absolut Vodka'        => ['spirits', 'drink', [
                ['Blue', '750 ml', 'mixed', 25, 5000, 2.5, 80000, 40],
            ]],

            // ── WHISKY ────────────────────────────────────────────────────────
            'Grants Whisky'        => ['spirits', 'drink', [
                ['Standard', '750 ml', 'mixed', 25, 4000, 2, 70000, 35],
            ]],
            'Jack Daniels'         => ['spirits', 'drink', [
                ['No. 7',   '750 ml', 'mixed', 25, 8000, 4, 120000, 60],
                ['Honey',   '750 ml', 'mixed', 25, 8000, 4, 120000, 60],
            ]],
            'Johnnie Walker'       => ['spirits', 'drink', [
                ['Red Label',   '750 ml', 'mixed', 25, 5000, 2.5, 80000, 40],
                ['Black Label',  '750 ml', 'mixed', 25, 8000, 4,   140000, 70],
            ]],
            'Jameson'              => ['spirits', 'drink', [
                ['Irish Whiskey', '750 ml', 'mixed', 25, 6000, 3, 100000, 50],
            ]],
            'J&B Rare'             => ['spirits', 'drink', [
                ['Standard', '750 ml', 'mixed', 25, 4000, 2, 70000, 35],
            ]],

            // ── RUM ───────────────────────────────────────────────────────────
            'Bacardi'              => ['spirits', 'drink', [
                ['White', '750 ml', 'mixed', 25, 4000, 2, 70000, 35],
            ]],
            'Captain Morgan'       => ['spirits', 'drink', [
                ['Spiced Gold', '750 ml', 'mixed', 25, 4000, 2, 70000, 35],
            ]],

            // ── COGNAC / LIQUEUR ─────────────────────────────────────────────
            'Hennessy'             => ['spirits', 'drink', [
                ['VS',   '750 ml', 'mixed', 25, 10000, 5, 200000, 100],
                ['VSOP', '750 ml', 'mixed', 25, 15000, 7.5, 280000, 140],
            ]],
            'Amarula'              => ['spirits', 'drink', [
                ['Cream', '750 ml', 'mixed', 15, 4000, 2, 50000, 25],
            ]],
            'Jägermeister'         => ['spirits', 'drink', [
                ['Standard', '750 ml', 'mixed', 25, 5000, 2.5, 90000, 45],
            ]],
            'Camino White'         => ['spirits', 'drink', [
                ['Tequila', '750 ml', 'mixed', 25, 4000, 2, 70000, 35],
            ]],

            // ── COCKTAILS ────────────────────────────────────────────────────
            'Cocktails'            => ['cocktails', 'drink', [
                ['Mojito',        'Glass', 'pic', 1, 0, 0, 15000, 7],
                ['Blue Lagoon',   'Glass', 'pic', 1, 0, 0, 15000, 7],
                ['Margarita',     'Glass', 'pic', 1, 0, 0, 18000, 8],
                ['Pina Colada',   'Glass', 'pic', 1, 0, 0, 18000, 8],
                ['Moscow Mule',   'Glass', 'pic', 1, 0, 0, 18000, 8],
                ['Traffic Light', 'Glass', 'pic', 1, 0, 0, 18000, 8],
                ['Endless Love',  'Glass', 'pic', 1, 0, 0, 18000, 8],
                ['Crazy in Love', 'Glass', 'pic', 1, 0, 0, 18000, 8],
                ['Blue Hawaiian', 'Glass', 'pic', 1, 0, 0, 18000, 8],
            ]],

            // ── MOCKTAILS ────────────────────────────────────────────────────
            'Mocktails'            => ['cocktails', 'drink', [
                ['Virgin Mojito',               'Glass', 'pic', 1, 0, 0, 10000, 5],
                ['Primeland Special Mocktail',  'Glass', 'pic', 1, 0, 0, 12000, 6],
            ]],

            // ── HOT BEVERAGES ────────────────────────────────────────────────
            'Hot Beverages'        => ['hot_beverages', 'drink', [
                ['Tea Masala',      'Cup', 'pic', 1, 0, 0, 5000, 3],
                ['Black Tea',       'Cup', 'pic', 1, 0, 0, 4000, 2],
                ['Ginger Tea',      'Cup', 'pic', 1, 0, 0, 4000, 2],
                ['Black Coffee',    'Cup', 'pic', 1, 0, 0, 5000, 3],
                ['Espresso',        'Cup', 'pic', 1, 0, 0, 5000, 3],
                ['Hot Chocolate',   'Cup', 'pic', 1, 0, 0, 6000, 3],
                ['Iced Coffee Black','Cup', 'pic', 1, 0, 0, 6000, 3],
                ['Iced Coffee Latte','Cup', 'pic', 1, 0, 0, 7000, 4],
            ]],

            // ── SOFT DRINKS / WATER ──────────────────────────────────────────
            'Kilimanjaro Water'    => ['water', 'drink', [
                ['Small (500ml)',   '500 ml', 'pic', 1, 0, 0, 1000, 1],
                ['Large (1.5L)',    '1.5 l',  'pic', 1, 0, 0, 2000, 1],
            ]],
            'Soda'                 => ['non_alcoholic_beverage', 'drink', [
                ['Standard',  '350 ml', 'pic', 1, 0, 0, 1500, 1],
            ]],
            'Fresh Juice'          => ['juices', 'drink', [
                ['Glass', 'Glass', 'pic', 1, 0, 0, 6000, 3],
            ]],
            'Mixed Fruit Smoothie' => ['juices', 'drink', [
                ['Glass', 'Glass', 'pic', 1, 0, 0, 8000, 4],
            ]],
            'Red Bull'             => ['energy_drinks', 'drink', [
                ['Standard', '250 ml', 'pic', 1, 0, 0, 6000, 3],
            ]],
            'Bavaria'              => ['non_alcoholic_beverage', 'drink', [
                ['Standard', '330 ml', 'pic', 1, 0, 0, 4000, 2],
            ]],
            'Grand Malt'           => ['non_alcoholic_beverage', 'drink', [
                ['Standard', '330 ml', 'pic', 1, 0, 0, 4000, 2],
            ]],
            'Ceres Juice'          => ['juices', 'drink', [
                ['1 Litre',  '1 l',   'pic', 1, 0, 0, 10000, 5],
                ['250ml',    '250 ml','pic', 1, 0, 0, 3000, 2],
            ]],
            'Azam Juice'           => ['juices', 'drink', [
                ['Small', '250 ml', 'pic', 1, 0, 0, 1500, 1],
            ]],

            // ── CHAMPAGNE ────────────────────────────────────────────────────
            'Champagne'            => ['wines', 'drink', [
                ['Standard', '750 ml', 'mixed', 6, 0, 0, 120000, 50],
            ]],
        ];

        $totalVariants = 0;

        foreach ($brands as $brandName => [$category, $type, $variants]) {
            $product = Product::firstOrCreate(
                ['name' => $brandName],
                [
                    'category'     => $category,
                    'type'         => $type,
                    'brand_or_type'=> $brandName,
                    'description'  => 'PrimeLand Hotel Menu Item',
                    'is_active'    => true,
                    'supplier_id'  => null,
                ]
            );

            foreach ($variants as $index => [$varName, $size, $method, $servings, $priceServing, $priceServingUSD, $pricePic, $pricePicUSD]) {
                $canPic     = in_array($method, ['pic', 'mixed']);
                $canServing = in_array($method, ['glass', 'mixed']);

                ProductVariant::updateOrCreate(
                    [
                        'product_id'   => $product->id,
                        'variant_name' => $varName,
                        'measurement'  => $size,
                    ],
                    [
                        'packaging'               => 'unit',
                        'items_per_package'       => 1,
                        'display_order'           => $index,
                        'is_active'               => true,
                        'servings_per_pic'        => $servings > 1 ? $servings : 1,
                        'selling_unit'            => $canServing ? 'glass' : 'pic',
                        'can_sell_as_pic'         => $canPic,
                        'can_sell_as_serving'     => $canServing,
                        'selling_price_per_serving' => $priceServing > 0 ? $priceServing : null,
                        'selling_price_per_serving_usd' => $priceServingUSD > 0 ? $priceServingUSD : null,
                        'selling_price_per_pic'   => $pricePic > 0 ? $pricePic : null,
                        'selling_price_per_pic_usd'   => $pricePicUSD > 0 ? $pricePicUSD : null,
                    ]
                );

                $totalVariants++;
            }
        }

        $this->command->info('  → Drinks menu seeded (' . count($brands) . ' brands, ' . $totalVariants . ' variants)');
    }
}

