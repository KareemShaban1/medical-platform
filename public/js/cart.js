/**
 * Cart functionality for adding products
 * Usage: Add data-product-id and data-supplier-id attributes to "Add to Cart" buttons
 */

function addToCart(productId, supplierId = null, quantity = 1) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }

    // Show loading state if button exists
    const button = event?.target;
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count badge
            const cartBadge = document.getElementById('cart-count-badge');
            if (cartBadge) {
                cartBadge.textContent = data.cart_count;
                cartBadge.style.display = data.cart_count > 0 ? 'flex' : 'none';
            }

            // Show success message
            if (typeof toast_success === 'function') {
                toast_success(data.message);
            } else {
                alert(data.message);
            }

            // Reload cart dropdown data if function exists
            if (typeof loadCartData === 'function') {
                loadCartData();
            }
        } else {
            if (typeof toast_error === 'function') {
                toast_error(data.message);
            } else {
                alert(data.message);
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

// Auto-bind click events to buttons with data-add-to-cart attribute
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-add-to-cart]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const supplierId = this.dataset.supplierId || null;
            const quantity = parseInt(this.dataset.quantity) || 1;
            
            if (productId) {
                addToCart(productId, supplierId, quantity);
            }
        });
    });
});
