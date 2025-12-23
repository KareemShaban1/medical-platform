/**
 * Cart functionality for adding products
 * Usage: Add data-product-id and data-supplier-id attributes to "Add to Cart" buttons
 */

function addToCart(productId, supplierId = null, quantity = 1, button = null) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }

    // Show loading state if button exists
    const originalText = button?.textContent;
    if (button) {
        button.disabled = true;
        button.textContent = 'Adding...';
    }

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            product_id: productId,
            supplier_id: supplierId,
            quantity: quantity
        })
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(data => {
        if (data.ok && data.data.success) {
            // Update cart count badge
            const cartBadge = document.getElementById('cart-count-badge');
            if (cartBadge) {
                cartBadge.textContent = data.data.cart_count;
                cartBadge.style.display = data.data.cart_count > 0 ? 'flex' : 'none';
            }

            // Show success message
            if (typeof toast_success === 'function') {
                toast_success(data.data.message);
            } else {
                alert(data.data.message);
            }

            // Reload cart dropdown data if function exists
            if (typeof loadCartData === 'function') {
                loadCartData();
            }
        } else {
            if (typeof toast_error === 'function') {
                toast_error(data.data?.message || 'Failed to add product to cart');
            } else {
                alert(data.data?.message || 'Failed to add product to cart');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof toast_error === 'function') {
            toast_error('Failed to add product to cart');
            // redirect to clinic/login
            window.location.href = '/clinic/login';
        } else {
            alert('Failed to add product to cart');
        }
    })
    .finally(() => {
        if (button) {
            button.disabled = false;
            button.textContent = originalText;
        }
    });
}

// Delegate click events for dynamically loaded product cards
document.addEventListener('click', function(e) {
    const button = e.target.closest('[data-add-to-cart]');
    if (!button) {
        return;
    }
    e.preventDefault();

    const productId = button.dataset.productId;
    const supplierId = button.dataset.supplierId || null;
    const quantity = parseInt(button.dataset.quantity) || 1;

    if (productId) {
        addToCart(productId, supplierId, quantity, button);
    }
});
