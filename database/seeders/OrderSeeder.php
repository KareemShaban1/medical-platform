<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSupplier;
use App\Models\Refund;
use App\Models\Clinic;
use App\Models\ClinicUser;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Get some existing data
            $clinics = Clinic::take(5)->get();
            $clinicUsers = ClinicUser::take(10)->get();
            $suppliers = Supplier::take(5)->get();
            $products = Product::take(20)->get();

            if ($clinics->isEmpty() || $clinicUsers->isEmpty() || $suppliers->isEmpty() || $products->isEmpty()) {
                $this->command->warn('Please ensure you have clinics, clinic users, suppliers, and products in your database before running this seeder.');
                return;
            }

            // Create 20 orders
            for ($i = 1; $i <= 20; $i++) {
                $clinic = $clinics->random();
                $clinicUser = $clinicUsers->where('clinic_id', $clinic->id)->first() ?? $clinicUsers->random();

                $order = Order::create([
                    'clinic_user_id' => $clinicUser->id,
                    'clinic_id' => $clinic->id,
                    'number' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'status' => collect(['pending', 'processing', 'delivering', 'completed', 'cancelled'])->random(),
                    'total' => 0, // Will be calculated later
                    'shipping' => rand(5, 25),
                    'tax' => rand(10, 50),
                    'discount' => rand(0, 20),
                    'payment_method' => rand(0, 1),
                    'payment_status' => collect(['pending', 'paid', 'failed'])->random(),
                ]);

                // Select 2-5 random suppliers for this order
                $orderSuppliers = $suppliers->random(rand(2, min(5, $suppliers->count())));
                $totalAmount = 0;

                foreach ($orderSuppliers as $supplier) {
                    // Create 1-4 items per supplier
                    $itemCount = rand(1, 4);
                    $supplierSubtotal = 0;

                    for ($j = 0; $j < $itemCount; $j++) {
                        $product = $products->random();
                        $quantity = rand(1, 5);
                        $price = rand(10, 200);
                        $itemTotal = $quantity * $price;

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'supplier_id' => $supplier->id,
                            'quantity' => $quantity,
                            'price' => $price,
                            'status' => collect(['pending', 'processing', 'delivering', 'completed', 'cancelled'])->random(),
                        ]);

                        $supplierSubtotal += $itemTotal;
                    }

                    // Create order supplier record
                    OrderSupplier::create([
                        'order_id' => $order->id,
                        'supplier_id' => $supplier->id,
                        'subtotal' => $supplierSubtotal,
                        'status' => collect(['pending', 'processing', 'delivering', 'completed', 'cancelled'])->random(),
                    ]);

                    $totalAmount += $supplierSubtotal;
                }

                // Update order total
                $finalTotal = $totalAmount + $order->shipping + $order->tax - $order->discount;
                $order->update(['total' => $finalTotal]);

                // Create some refunds (30% chance per order)
                if (rand(1, 100) <= 30) {
                    $refundSupplier = $orderSuppliers->random();
                    $orderItems = OrderItem::where('order_id', $order->id)
                        ->where('supplier_id', $refundSupplier->id)
                        ->get();

                    if ($orderItems->isNotEmpty()) {
                        $refundType = collect(['full', 'partial'])->random();
                        $refundAmount = $refundType === 'full'
                            ? $orderItems->sum(fn($item) => $item->quantity * $item->price)
                            : rand(10, 100);

                        Refund::create([
                            'order_id' => $order->id,
                            'order_item_id' => $refundType === 'partial' ? $orderItems->random()->id : null,
                            'supplier_id' => $refundSupplier->id,
                            'amount' => $refundAmount,
                            'refund_type' => $refundType,
                            'status' => collect(['pending', 'approved', 'rejected', 'processed'])->random(),
                            'reason' => collect([
                                'Product damaged during shipping',
                                'Wrong product delivered',
                                'Customer changed mind',
                                'Product quality issues',
                                'Delivery delay'
                            ])->random(),
                        ]);
                    }
                }
            }

            $this->command->info('Created 20 orders with items, suppliers, and some refunds.');
        });
    }
}
