<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Inventory - Supermarket</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; margin-bottom: 30px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .nav-buttons { display: flex; gap: 10px; margin-bottom: 30px; }
        .nav-btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
        .btn-sell { background: #FF9800; color: white; }
        .btn-store { background: #4CAF50; color: white; }
        form { background: #f9f9f9; padding: 20px; margin-bottom: 30px; border-radius: 5px; }
        input, select { padding: 10px; margin: 5px; width: 200px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-add { background: #4CAF50; color: white; padding: 10px 20px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-remove { background: #f44336; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-edit-price {  color: white; padding: 4px 8px; border: #333; border-radius: 3px; cursor: pointer; font-size: 12px; margin-left: 5px; }
        .price-input { width: 100px; padding: 4px; border: 1px solid #2196F3; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #333; color: white; }
        tr:hover { background: #f5f5f5; }
        .message { padding: 12px; margin-bottom: 20px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Inventory</h1>

        <div class="nav-buttons">
            <a href="{{ route('selling') }}" class="nav-btn btn-sell">Back to Selling</a>
            <a href="{{ route('store') }}" class="nav-btn btn-store">View Store</a>
        </div>

        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        <div id="ajax-message" class="message success" style="display:none;"></div>

        <form id="add-product-form" method="POST" action="{{ route('products.store') }}">
            @csrf
            <h3>Add New Product</h3>
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" name="price" placeholder="Unit Price" step="0.01" required>
            <input type="number" name="stock" placeholder="Quantity" required>
            <button type="submit" class="btn-add">Add Product</button>
        </form>

        <form id="update-stock-form" method="POST" action="{{ route('products.updateStock') }}">
            @csrf
            @method('PUT')
            <h3>Add Stock by Product ID</h3>
            <select name="product_id" id="stock-product-id" required>
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">ID: {{ $product->id }} - {{ $product->name }} (Current: {{ $product->stock }})</option>
                @endforeach
            </select>
            <input type="number" name="quantity" placeholder="Quantity to Add" id="stock-quantity" required min="1">
            <button type="submit" class="btn-add">Add Stock</button>
        </form>

        @if($products->count() > 0)
            <table id="products-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>
                            <span id="price-display-{{ $product->id }}">LKR {{ number_format($product->price, 2) }}</span>
                            <input type="number" id="price-input-{{ $product->id }}" class="price-input" value="{{ $product->price }}" step="0.01" style="display:none;">
                            <button class="btn-edit-price" data-product-id="{{ $product->id }}" onclick="editPrice(this.dataset.productId)">✏️</button>
                        </td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            <button class="btn-remove" data-product-id="{{ $product->id }}" onclick="deleteProduct(this.dataset.productId)">Remove</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No products in inventory. Add your first product above!</p>
        @endif
    </div>

    <script type="application/json" id="products-data">@json($products)</script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let products = JSON.parse(document.getElementById('products-data').textContent);

        // Show message
        function showMessage(message, isSuccess = true) {
            const msgDiv = document.getElementById('ajax-message');
            msgDiv.textContent = message;
            msgDiv.className = isSuccess ? 'message success' : 'message error';
            msgDiv.style.display = 'block';
            setTimeout(() => msgDiv.style.display = 'none', 3000);
        }

        // Update table
        function updateTable(productsData) {
            products = productsData;
            const tbody = document.querySelector('#products-table tbody');
            
            if (productsData.length === 0) {
                document.getElementById('products-table').style.display = 'none';
                return;
            }

            tbody.innerHTML = productsData.map(p => `
                <tr>
                    <td>${p.id}</td>
                    <td>${p.name}</td>
                    <td>
                        <span id="price-display-${p.id}">LKR ${parseFloat(p.price).toFixed(2)}</span>
                        <input type="number" id="price-input-${p.id}" class="price-input" value="${p.price}" step="0.01" style="display:none;">
                        <button class="btn-edit-price" onclick="editPrice(${p.id})">✏️</button>
                    </td>
                    <td>${p.stock}</td>
                    <td>
                        <button class="btn-remove" onclick="deleteProduct(${p.id})">Remove</button>
                    </td>
                </tr>
            `).join('');
            updateStockSelect(productsData);
        }

        // Update stock dropdown
        function updateStockSelect(productsData) {
            const select = document.getElementById('stock-product-id');
            select.innerHTML = '<option value="">-- Select Product --</option>' +
                productsData.map(p => 
                    `<option value="${p.id}">ID: ${p.id} - ${p.name} (Current: ${p.stock})</option>`
                ).join('');
        }

        // Add product
        document.getElementById('add-product-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route("products.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    showMessage(data.message);
                    updateTable(data.products);
                    this.reset();
                }
            } catch (error) {
                showMessage('Error adding product', false);
            }
        });

        // Update stock
        document.getElementById('update-stock-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('product_id', document.getElementById('stock-product-id').value);
            formData.append('quantity', document.getElementById('stock-quantity').value);

            try {
                const response = await fetch('{{ route("products.updateStock") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    showMessage(data.message);
                    updateTable(data.products);
                    this.reset();
                }
            } catch (error) {
                showMessage('Error updating stock', false);
            }
        });

        // Delete product
        async function deleteProduct(id) {
            if (!confirm('Remove this product?')) return;

            try {
                const response = await fetch('/manage/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    showMessage(data.message);
                    updateTable(data.products);
                }
            } catch (error) {
                console.error('Delete error:', error);
                showMessage('Error deleting product', false);
            }
        }

        // Edit price
        function editPrice(id) {
            const display = document.getElementById('price-display-' + id);
            const input = document.getElementById('price-input-' + id);
            
            if (input.style.display === 'none') {
                display.style.display = 'none';
                input.style.display = 'inline';
                input.focus();
            } else {
                updatePrice(id);
            }
        }

        // Update price
        async function updatePrice(id) {
            const input = document.getElementById('price-input-' + id);
            const newPrice = parseFloat(input.value);
            
            if (isNaN(newPrice) || newPrice <= 0) {
                showMessage('Invalid price', false);
                return;
            }
            
            try {
                const response = await fetch('/manage/update-price', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: id,
                        price: newPrice
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showMessage(data.message);
                    updateTable(data.products);
                } else {
                    showMessage(data.message || 'Failed to update price', false);
                }
            } catch (error) {
                showMessage('Error updating price', false);
            }
        }
    </script>
</body>
</html>
