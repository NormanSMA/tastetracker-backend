<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Area;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==================== USUARIOS ====================

        // Administradores
        $admin1 = User::updateOrCreate(
            ['email' => 'nsma@tastetracker.com'],
            [
                'name' => 'Norman Smith',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '8888-8888',
                'is_active' => true,
            ]
        );

        $admin2 = User::updateOrCreate(
            ['email' => 'maria.gonzalez@tastetracker.com'],
            [
                'name' => 'María González',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '8888-8889',
                'is_active' => true,
            ]
        );

        // Meseros
        $waiter1 = User::updateOrCreate(
            ['email' => 'anton@tastetracker.com'],
            [
                'name' => 'Antonio Morales',
                'password' => Hash::make('password'),
                'role' => 'waiter',
                'phone' => '8888-1111',
                'is_active' => true,
            ]
        );

        $waiter2 = User::updateOrCreate(
            ['email' => 'carmen.lopez@tastetracker.com'],
            [
                'name' => 'Carmen López',
                'password' => Hash::make('password'),
                'role' => 'waiter',
                'phone' => '8888-1112',
                'is_active' => true,
            ]
        );

        $waiter3 = User::updateOrCreate(
            ['email' => 'diego.ramirez@tastetracker.com'],
            [
                'name' => 'Diego Ramírez',
                'password' => Hash::make('password'),
                'role' => 'waiter',
                'phone' => '8888-1113',
                'is_active' => true,
            ]
        );

        $waiter4 = User::updateOrCreate(
            ['email' => 'laura.sanchez@tastetracker.com'],
            [
                'name' => 'Laura Sánchez',
                'password' => Hash::make('password'),
                'role' => 'waiter',
                'phone' => '8888-1114',
                'is_active' => false, // Inactivo
            ]
        );

        // Clientes
        $customer1 = User::create([
            'name' => 'Pedro Martínez',
            'email' => 'pedro.martinez@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '7777-2221',
            'is_active' => true,
        ]);

        $customer2 = User::create([
            'name' => 'Ana Rodríguez',
            'email' => 'ana.rodriguez@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '7777-2222',
            'is_active' => true,
        ]);

        $customer3 = User::create([
            'name' => 'Carlos Fernández',
            'email' => 'carlos.fernandez@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '7777-2223',
            'is_active' => true,
        ]);

        $customer4 = User::create([
            'name' => 'Sofía Torres',
            'email' => 'sofia.torres@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '7777-2224',
            'is_active' => true,
        ]);

        $customer5 = User::create([
            'name' => 'Miguel Ángel Ruiz',
            'email' => 'miguel.ruiz@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '7777-2225',
            'is_active' => true,
        ]);

        // ==================== ÁREAS ====================

        $area1 = Area::updateOrCreate(
            ['name' => 'Salón Principal'],
            [
                'prefix' => 'S',
                'total_tables' => 6,
                'description' => 'Área con aire acondicionado y capacidad para 50 personas'
            ]
        );

        $area2 = Area::updateOrCreate(
            ['name' => 'Terraza'],
            [
                'prefix' => 'T',
                'total_tables' => 6,
                'description' => 'Área al aire libre para fumadores con vista al jardín'
            ]
        );

        $area3 = Area::updateOrCreate(
            ['name' => 'Barra'],
            [
                'prefix' => 'B',
                'total_tables' => 8,
                'description' => 'Asientos altos cerca de las bebidas, ambiente casual'
            ]
        );

        $area4 = Area::updateOrCreate(
            ['name' => 'Salón VIP'],
            [
                'prefix' => 'V',
                'total_tables' => 4,
                'description' => 'Área privada para eventos especiales y reuniones'
            ]
        );

        $area5 = Area::updateOrCreate(
            ['name' => 'Jardín'],
            [
                'prefix' => 'J',
                'total_tables' => 6,
                'description' => 'Espacio al aire libre con mesas bajo sombrillas'
            ]
        );

        // ==================== CATEGORÍAS ====================

        $catEntradas = Category::create([
            'name' => 'Entradas',
            'slug' => 'entradas',
            'image' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&q=80'
        ]);

        $catPlatosFuertes = Category::create([
            'name' => 'Platos Fuertes',
            'slug' => 'platos-fuertes',
            'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&q=80'
        ]);

        $catBebidas = Category::create([
            'name' => 'Bebidas',
            'slug' => 'bebidas',
            'image' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=800&q=80'
        ]);

        $catPostres = Category::create([
            'name' => 'Postres',
            'slug' => 'postres',
            'image' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=800&q=80'
        ]);

        $catEnsaladas = Category::create([
            'name' => 'Ensaladas',
            'slug' => 'ensaladas',
            'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&q=80'
        ]);

        $catPizzas = Category::create([
            'name' => 'Pizzas',
            'slug' => 'pizzas',
            'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&q=80'
        ]);

        // ==================== PRODUCTOS ====================

        // ENTRADAS
        $prod1 = Product::create([
            'category_id' => $catEntradas->id,
            'name' => 'Bruschetta Italiana',
            'slug' => 'bruschetta-italiana',
            'description' => 'Pan tostado con tomate fresco, albahaca, ajo y aceite de oliva',
            'price' => 328.14,
            'image' => 'https://images.unsplash.com/photo-1572695157366-5e585ab2b69f?w=800&q=80',
            'is_active' => true,
        ]);

        $prod2 = Product::create([
            'category_id' => $catEntradas->id,
            'name' => 'Alitas Picantes',
            'slug' => 'alitas-picantes',
            'description' => 'Alitas de pollo con salsa búfalo y aderezo ranch',
            'price' => 456.25,
            'image' => 'https://images.unsplash.com/photo-1608039829572-78524f79c4c7?w=800&q=80',
            'is_active' => true,
        ]);

        $prod3 = Product::create([
            'category_id' => $catEntradas->id,
            'name' => 'Camarones al Ajillo',
            'slug' => 'camarones-al-ajillo',
            'description' => 'Camarones salteados en mantequilla, ajo y vino blanco',
            'price' => 583.64,
            'image' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?w=800&q=80',
            'is_active' => true,
        ]);

        $prod4 = Product::create([
            'category_id' => $catEntradas->id,
            'name' => 'Nachos Supreme',
            'slug' => 'nachos-supreme',
            'description' => 'Tortillas con queso, guacamole, crema y jalapeños',
            'price' => 401.14,
            'image' => 'https://images.unsplash.com/photo-1513456852971-30c0b8199d4d?w=800&q=80',
            'is_active' => true,
        ]);

        $prod5 = Product::create([
            'category_id' => $catEntradas->id,
            'name' => 'Tabla de Quesos',
            'slug' => 'tabla-de-quesos',
            'description' => 'Selección de quesos artesanales con frutas y nueces',
            'price' => 675.25,
            'image' => 'https://images.unsplash.com/photo-1452195100486-9cc805987862?w=800&q=80',
            'is_active' => true,
        ]);

        // PLATOS FUERTES
        $prod6 = Product::create([
            'category_id' => $catPlatosFuertes->id,
            'name' => 'Filete Mignon',
            'slug' => 'filete-mignon',
            'description' => 'Filete de res premium con puré de papas y vegetales asados',
            'price' => 600,
            'image' => 'https://picsum.photos/seed/filete-mignon/800/600',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPlatosFuertes->id,
            'name' => 'Salmón a la Parrilla',
            'slug' => 'salmon-a-la-parrilla',
            'description' => 'Filete de salmón con salsa de limón y hierbas frescas',
            'price' => 550,
            'image' => 'https://picsum.photos/seed/salmon-parrilla/800/600',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPlatosFuertes->id,
            'name' => 'Pasta Carbonara',
            'slug' => 'pasta-carbonara',
            'description' => 'Pasta con salsa cremosa de huevo, queso parmesano y panceta',
            'price' => 420,
            'image' => 'https://picsum.photos/seed/pasta-carbonara/800/600',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPlatosFuertes->id,
            'name' => 'Pollo Teriyaki',
            'slug' => 'pollo-teriyaki',
            'description' => 'Pechuga de pollo con salsa teriyaki y arroz integral',
            'price' => 360,
            'image' => 'https://picsum.photos/seed/pollo-teriyaki/800/600',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPlatosFuertes->id,
            'name' => 'Costillas BBQ',
            'slug' => 'costillas-bbq',
            'description' => 'Costillas de cerdo glaseadas con salsa barbecue casera',
            'price' => 500,
            'image' => 'https://picsum.photos/seed/costillas-bbq/800/600',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPlatosFuertes->id,
            'name' => 'Paella Valenciana',
            'slug' => 'paella-valenciana',
            'description' => 'Arroz con mariscos, pollo y azafrán al estilo valenciano',
            'price' => 480,
            'image' => 'https://picsum.photos/seed/paella-valenciana/800/600',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPlatosFuertes->id,
            'name' => 'Lasaña Boloñesa',
            'slug' => 'lasana-bolonesa',
            'description' => 'Capas de pasta con carne, bechamel y queso gratinado',
            'price' => 400,
            'image' => 'https://picsum.photos/seed/lasana-bolonesa/800/600',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPlatosFuertes->id,
            'name' => 'Tacos al Pastor',
            'slug' => 'tacos-al-pastor',
            'description' => 'Tacos de cerdo marinado con piña, cilantro y cebolla',
            'price' => 320,
            'image' => 'https://picsum.photos/seed/tacos-al-pastor/800/600',
            'is_active' => true,
        ]);

        // PIZZAS
        Product::create([
            'category_id' => $catPizzas->id,
            'name' => 'Pizza Margherita',
            'slug' => 'pizza-margherita',
            'description' => 'Salsa de tomate, mozzarella fresca y albahaca',
            'price' => 620.14,
            'image' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPizzas->id,
            'name' => 'Pizza Pepperoni',
            'slug' => 'pizza-pepperoni',
            'description' => 'Salsa de tomate, mozzarella y abundante pepperoni',
            'price' => 675.25,
            'image' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPizzas->id,
            'name' => 'Pizza Cuatro Quesos',
            'slug' => 'pizza-cuatro-quesos',
            'description' => 'Mozzarella, gorgonzola, parmesano y queso de cabra',
            'price' => 766.14,
            'image' => 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPizzas->id,
            'name' => 'Pizza Hawaiana',
            'slug' => 'pizza-hawaiana',
            'description' => 'Jamón, piña, mozzarella y salsa de tomate',
            'price' => 638.75,
            'image' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&q=80',
            'is_active' => true,
        ]);

        // ENSALADAS
        Product::create([
            'category_id' => $catEnsaladas->id,
            'name' => 'Ensalada César',
            'slug' => 'ensalada-cesar',
            'description' => 'Lechuga romana, pollo, crutones, parmesano y aderezo césar',
            'price' => 510.64,
            'image' => 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catEnsaladas->id,
            'name' => 'Ensalada Caprese',
            'slug' => 'ensalada-caprese',
            'description' => 'Tomate, mozzarella fresca, albahaca y vinagre balsámico',
            'price' => 419.75,
            'image' => 'https://images.unsplash.com/photo-1592417817038-d13fd7342656?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catEnsaladas->id,
            'name' => 'Ensalada Griega',
            'slug' => 'ensalada-griega',
            'description' => 'Pepino, tomate, aceitunas, queso feta y aderezo mediterráneo',
            'price' => 474.14,
            'image' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&q=80',
            'is_active' => true,
        ]);

        // BEBIDAS
        Product::create([
            'category_id' => $catBebidas->id,
            'name' => 'Limonada Natural',
            'slug' => 'limonada-natural',
            'description' => 'Limonada fresca hecha con limones recién exprimidos',
            'price' => 164.25,
            'image' => 'https://images.unsplash.com/photo-1523677011781-c91d1bbe2f8d?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catBebidas->id,
            'name' => 'Café Espresso',
            'slug' => 'cafe-espresso',
            'description' => 'Café italiano preparado en máquina espresso',
            'price' => 127.75,
            'image' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catBebidas->id,
            'name' => 'Smoothie de Frutos Rojos',
            'slug' => 'smoothie-frutos-rojos',
            'description' => 'Batido de fresa, frambuesa, arándanos y yogurt',
            'price' => 255.14,
            'image' => 'https://images.unsplash.com/photo-1505252585461-04db1eb84625?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catBebidas->id,
            'name' => 'Cerveza Artesanal IPA',
            'slug' => 'cerveza-artesanal-ipa',
            'description' => 'Cerveza artesanal estilo IPA con notas cítricas',
            'price' => 273.75,
            'image' => 'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catBebidas->id,
            'name' => 'Vino Tinto Reserva',
            'slug' => 'vino-tinto-reserva',
            'description' => 'Copa de vino tinto de la casa, reserva especial',
            'price' => 364.64,
            'image' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catBebidas->id,
            'name' => 'Mojito Clásico',
            'slug' => 'mojito-clasico',
            'description' => 'Cóctel refrescante de ron, menta, limón y soda',
            'price' => 310.25,
            'image' => 'https://images.unsplash.com/photo-1551538827-9c037cc4f71a?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catBebidas->id,
            'name' => 'Agua Mineral',
            'slug' => 'agua-mineral',
            'description' => 'Agua mineral natural con o sin gas',
            'price' => 91.25,
            'image' => 'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=800&q=80',
            'is_active' => true,
        ]);

        // POSTRES
        Product::create([
            'category_id' => $catPostres->id,
            'name' => 'Tiramisú',
            'slug' => 'tiramisu',
            'description' => 'Postre italiano con café, mascarpone y cacao',
            'price' => 328.14,
            'image' => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPostres->id,
            'name' => 'Cheesecake de Frutos Rojos',
            'slug' => 'cheesecake-frutos-rojos',
            'description' => 'Tarta de queso con coulis de frutos del bosque',
            'price' => 346.75,
            'image' => 'https://images.unsplash.com/photo-1533134242820-b4f29f04ca83?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPostres->id,
            'name' => 'Brownie con Helado',
            'slug' => 'brownie-con-helado',
            'description' => 'Brownie de chocolate caliente con helado de vainilla',
            'price' => 291.64,
            'image' => 'https://images.unsplash.com/photo-1607920591413-4ec007e70023?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPostres->id,
            'name' => 'Crème Brûlée',
            'slug' => 'creme-brulee',
            'description' => 'Crema de vainilla con caramelo crujiente',
            'price' => 383.25,
            'image' => 'https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPostres->id,
            'name' => 'Helado Artesanal',
            'slug' => 'helado-artesanal',
            'description' => 'Tres bolas de helado artesanal a elegir',
            'price' => 237.25,
            'image' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=800&q=80',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $catPostres->id,
            'name' => 'Flan Casero',
            'slug' => 'flan-casero',
            'description' => 'Flan tradicional con caramelo líquido',
            'price' => 218.64,
            'image' => 'https://images.unsplash.com/photo-1624353365286-3f8d62daad51?w=800&q=80',
            'is_active' => true,
        ]);

        // ==================== ÓRDENES ====================
        // TODO: Sample orders can be added after verifying product IDs
        /*
        // Orden 1: Completada
        $order1 = Order::create([
            'user_id' => $customer1->id,
            'waiter_id' => $waiter1->id,
            'area_id' => $area1->id,
            'table_number' => 5,
            'status' => 'delivered',
            'order_type' => 'dine-in',
            'total' => 68.48,
            'notes' => 'Cliente solicita la carne término medio',
            'created_at' => now()->subDays(2),
        ]);

        OrderItem::create(['order_id' => $order1->id, 'product_id' => 6, 'quantity' => 1, 'unit_price' => 1204.14, 'subtotal' => 1204.14, 'notes' => 'Término medio']);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 328.14, 'subtotal' => 328.14]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => 18, 'quantity' => 2, 'unit_price' => 164.25, 'subtotal' => 328.50]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => 24, 'quantity' => 2, 'unit_price' => 328.14, 'subtotal' => 656.28]);
        */
    }
}
