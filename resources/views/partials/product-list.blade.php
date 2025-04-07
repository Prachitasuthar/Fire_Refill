@foreach ($products as $product)
    @php
        $imagePath = asset($product->image);
        $finalPrice = $product->price; // Default price

        // Check if the product has a valid (active) coupon
        if ($product->coupon_id) {
            $coupon = \DB::table('coupons')
                ->where('id', $product->coupon_id)
                ->where('status', 'active')
                ->where('expiry_date', '>=', now()) // Ensure coupon is not expired
                ->whereColumn('used_count', '<', 'max_usage') // Ensure max usage is not exceeded
                ->first();

            if ($coupon) {
                $finalPrice = $coupon->final_price;
            }
        }
    @endphp

    <div class="col">
        <div class="card border border-dark shadow-sm h-100 d-flex flex-column">
            <div class="position-relative">
                <img src="{{ $imagePath }}" class="card-img-top img-fluid" alt="{{ $product->name }}"
                    style="height: 250px; object-fit: cover;">

                <!-- Price Display with Discount -->
                <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-3 py-1" style="font-size: 0.9rem;">
                    @if ($finalPrice < $product->price)
                        <span style="text-decoration: line-through; opacity: 0.7; font-size: 0.85rem;">
                            ₹{{ number_format($product->price, 2) }}
                        </span>
                        <span style="font-weight: bold; font-size: 1rem; margin-left: 5px;">
                            ₹{{ number_format($finalPrice, 2) }}
                        </span>
                    @else
                        <span style="font-weight: bold; font-size: 1rem;">
                            ₹{{ number_format($finalPrice, 2) }}/-
                        </span>
                    @endif
                </span>


                <div class="position-absolute top-0 end-0 m-2 d-flex gap-2">
                    <!-- View Product Button -->
                    <button class="btn p-2 rounded-circle shadow view-product" data-id="{{ $product->id }}"
                        data-category-id="{{ $product->category_id }}" data-provider-id="{{ $product->provider_id }}"
                        style="width: 30px; height: 30px; background: rgba(255, 255, 255, 0.8); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-eye text-dark"></i>
                    </button>

                    <!-- Add to Cart Button -->
                    <button class="btn p-2 rounded-circle shadow add-to-cart" data-id="{{ $product->id }}"
                        data-category-id="{{ $product->category_id }}" data-provider-id="{{ $product->provider_id }}"
                        style="width: 30px; height: 30px; background: rgba(255, 255, 255, 0.8); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart text-dark"></i>
                    </button>
                </div>
            </div>

            <div class="position-absolute bottom-0 start-0 w-100 text-center p-2"
                style="background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.3));">
                <h5 class="text-white fw-bold text-truncate m-0" style="font-size: 1.1rem;">
                    {{ Str::limit($product->name, 30) }}
                </h5>
            </div>
        </div>
    </div>
@endforeach
