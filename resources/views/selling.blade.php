<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Selling - Supermarket</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; margin-bottom: 30px; text-align: center; }
        h2 { text-align: center; margin-bottom: 20px; }
        .container { text-align: center; max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .nav-buttons { display: flex; gap: 10px; margin-bottom: 30px; justify-content: center; }
        .nav-btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
        .btn-manage { background: #2196F3; color: white; }
        .btn-store { background: #4CAF50; color: white; }
        .sell-form { background: #f9f9f9; padding: 30px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        label { display: block; text-align: center; margin-top: 15px; font-weight: bold; }
        select, input { padding: 12px; margin: 10px auto; width: 100%; max-width: 500px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; display: block; }
        .btn-sell { background: #FF9800; color: white; padding: 15px; width: 100%; max-width: 500px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; margin: 20px auto 0; display: block; }
        .btn-sell:hover { background: #F57C00; }
        .btn-payment { background: #4CAF50; color: white; padding: 15px; width: 100%; max-width: 500px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; margin: 10px auto 0; display: block; }
        .btn-payment:hover { background: #45a049; }
        .btn-email { background: #9C27B0; color: white; padding: 15px; width: 100%; max-width: 500px; border: none; border-radius: 4px; cursor: pointer; font-size: 18px; margin: 10px auto 0; display: block; }
        .btn-email:hover { background: #7B1FA2; }
        .bill-section { background: #fff3e0; padding: 20px; border-radius: 5px; margin: 20px auto; max-width: 500px; display: none; }
        .email-section { background: #e8f5e9; padding: 15px; border-radius: 5px; margin-top: 15px; display: none; }
        .bill-item { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ddd; }
        .bill-total { font-size: 20px; font-weight: bold; margin-top: 15px; text-align: right; }
        .message { padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .product-info { background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 15px auto; max-width: 500px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Supermarket Management System</h1>
        <h2>Selling Page</h2>

        <div class="nav-buttons">
            <a href="{{ route('manage') }}" class="nav-btn btn-manage">Manage Inventory</a>
            <a href="{{ route('store') }}" class="nav-btn btn-store">View Store</a>
        </div>

        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="message error">{{ session('error') }}</div>
        @endif

        <div id="ajax-message" class="message success" style="display:none;"></div>

        @if($products->count() > 0)
            <div class="sell-form">
                <h2>Make a Sale</h2>
                <form id="sell-form" method="POST" action="{{ route('sell.process') }}">
                    @csrf
                    <label>Select Product:</label>
                    <select name="product_id" id="product_id" required>
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-price="{{ $product->price }}" 
                                    data-stock="{{ $product->stock }}">
                                {{ $product->name }} (ID: {{ $product->id }}) - Stock: {{ $product->stock }}
                            </option>
                        @endforeach
                    </select>

                    <div id="product-details" class="product-info" style="display:none;">
                        <p><strong>Price:</strong> LKR <span id="price">0</span></p>
                        <p><strong>Available Stock:</strong> <span id="stock">0</span></p>
                    </div>

                    <label>Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" value="1" required>

                    <button type="submit" class="btn-sell">Add to Cart</button>
                </form>

                <div id="bill-section" class="bill-section">
                    <h3>Shopping Cart</h3>
                    <div id="bill-items"></div>
                    <div class="bill-total">Total: LKR <span id="bill-total">0.00</span></div>
                    <button type="button" class="btn-payment" onclick="completePayment()">Complete Payment</button>
                    <div id="email-section" class="email-section">
                        <input type="email" id="customer-email" placeholder="Customer Email (optional)" style="margin-top: 0;">
                        <button type="button" class="btn-email" onclick="sendBillEmail()">Send Bill via Email</button>
                    </div>
                </div>
            </div>
        @else
            <div class="message error">No products available. Please add products in Manage Inventory.</div>
        @endif
    </div>

    <script type="application/json" id="products-data">@json($products)</script>

    <script>
        console.log('Script loaded successfully');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let products = JSON.parse(document.getElementById('products-data').textContent);
        const selectElement = document.getElementById('product_id');
        const detailsDiv = document.getElementById('product-details');
        let billItems = [];
        let billTotal = 0;
        
        console.log('Initial setup complete');
        
        // Show product details
        selectElement.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const quantityInput = document.getElementById('quantity');
            if (this.value) {
                const stock = parseInt(selectedOption.dataset.stock);
                document.getElementById('price').textContent = selectedOption.dataset.price;
                document.getElementById('stock').textContent = stock;
                quantityInput.max = stock;
                quantityInput.value = 1;
                detailsDiv.style.display = 'block';
            } else {
                detailsDiv.style.display = 'none';
                quantityInput.removeAttribute('max');
            }
        });

        // Show message
        function showMessage(message, isSuccess = true) {
            const msgDiv = document.getElementById('ajax-message');
            msgDiv.textContent = message;
            msgDiv.className = isSuccess ? 'message success' : 'message error';
            msgDiv.style.display = 'block';
            setTimeout(() => msgDiv.style.display = 'none', 3000);
        }

        // Update product dropdown
        function updateProductSelect(productsData) {
            products = productsData;
            selectElement.innerHTML = '<option value="">-- Choose Product --</option>' +
                productsData.map(p => 
                    `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">
                        ${p.name} (ID: ${p.id}) - Stock: ${p.stock}
                    </option>`
                ).join('');
            detailsDiv.style.display = 'none';
        }

        // Add to cart
        document.getElementById('sell-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = document.getElementById('product_id').value;
            const quantity = parseInt(document.getElementById('quantity').value);
            const product = products.find(p => p.id == productId);
            
            if (!product) return;
            
            if (quantity > product.stock) {
                showMessage('Insufficient stock', false);
                return;
            }
            
            billItems.push({
                id: product.id,
                name: product.name,
                quantity: quantity,
                price: product.price,
                total: product.price * quantity
            });
            billTotal += product.price * quantity;
            updateBillDisplay();
            showMessage('Item added to cart');
            this.reset();
            detailsDiv.style.display = 'none';
        });

        // Update bill display
        function updateBillDisplay() {
            const billSection = document.getElementById('bill-section');
            const billItemsDiv = document.getElementById('bill-items');
            const billTotalSpan = document.getElementById('bill-total');
            
            if (billItems.length > 0) {
                billSection.style.display = 'block';
                billItemsDiv.innerHTML = billItems.map(item => 
                    `<div class="bill-item">
                        <span>${item.name} x ${item.quantity}</span>
                        <span>LKR ${item.total.toFixed(2)}</span>
                    </div>`
                ).join('');
                billTotalSpan.textContent = billTotal.toFixed(2);
            }
        }

        // Complete payment
        async function completePayment() {
            if (billItems.length === 0) {
                showMessage('Cart is empty', false);
                return;
            }
            
            try {
                let allSuccess = true;
                for (const item of billItems) {
                    const formData = new FormData();
                    formData.append('product_id', item.id);
                    formData.append('quantity', item.quantity);
                    
                    const response = await fetch('{{ route("sell.process") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        showMessage(data.message || 'Payment failed', false);
                        allSuccess = false;
                        return;
                    }
                    
                    if (data.products) {
                        updateProductSelect(data.products);
                    }
                }
                
                if (allSuccess) {
                    showMessage('Payment completed successfully!');
                    document.getElementById('email-section').style.display = 'block';
                }
            } catch (error) {
                showMessage('Error processing payment', false);
            }
        }

        // Send bill via email
        async function sendBillEmail() {
            const email = document.getElementById('customer-email').value;
            
            if (!email) {
                showMessage('Please enter customer email', false);
                return;
            }
            
            if (billItems.length === 0) {
                showMessage('No items in bill', false);
                return;
            }
            
            console.log('Saving bill with data:', {
                customer_email: email,
                items: billItems
            });
            
            try {
                // Save bill to database first
                const saveBillResponse = await fetch('{{ route("bill.save") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_email: email,
                        items: billItems
                    })
                });
                
                console.log('Save bill response status:', saveBillResponse.status);
                const saveBillData = await saveBillResponse.json();
                console.log('Save bill response data:', saveBillData);
                
                if (!saveBillData.success) {
                    showMessage(saveBillData.message || 'Failed to save bill', false);
                    return;
                }
                
                // Then send email with bill link
                const response = await fetch('{{ route("bill.email") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        bill_id: saveBillData.bill_id
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Reset cart first
                    billItems = [];
                    billTotal = 0;
                    
                    // Clear display
                    const billSection = document.getElementById('bill-section');
                    const billItemsDiv = document.getElementById('bill-items');
                    const billTotalSpan = document.getElementById('bill-total');
                    
                    billItemsDiv.innerHTML = '';
                    billTotalSpan.textContent = '0.00';
                    billSection.style.display = 'none';
                    
                    // Hide email section
                    document.getElementById('email-section').style.display = 'none';
                    document.getElementById('customer-email').value = '';
                    
                    // Show success message
                    showMessage(data.message);
                } else {
                    showMessage(data.message, false);
                }
            } catch (error) {
                showMessage('Error sending email', false);
            }
        }
    </script>
</body>
</html>
