/**
 * TechShop E-Commerce Custom JavaScript
 */

// Global AJAX Helper Functions
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toastId = 'toast-' + Date.now();
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

function updateCartBadge(count) {
    const badge = document.querySelector('.cart-badge, .badge.bg-danger');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Handle quantity change with delete on quantity 1
window.handleQuantityChange = function(button, cartId, currentQuantity) {
    const form = button.closest('form');
    const quantityInput = form.querySelector('.quantity-input');
    
    if (currentQuantity === 1) {
        // Show delete confirmation
        if (confirm('Hapus item ini dari keranjang?')) {
            // Trigger delete form
            const deleteForm = document.querySelector(`.cart-remove-form[data-cart-id="${cartId}"]`);
            if (deleteForm) {
                deleteForm.dispatchEvent(new Event('submit'));
            }
        }
    } else {
        // Decrease quantity
        quantityInput.stepDown();
        form.dispatchEvent(new Event('submit'));
    }
}

// Update minus button icon based on quantity
function updateMinusButtons() {
    document.querySelectorAll('.cart-update-form').forEach(form => {
        const quantityInput = form.querySelector('.quantity-input');
        const minusButton = form.querySelector('.btn-minus');
        const minusIcon = minusButton?.querySelector('i');
        
        if (quantityInput && minusButton && minusIcon) {
            const quantity = parseInt(quantityInput.value);
            if (quantity === 1) {
                minusIcon.className = 'bi bi-trash';
                minusButton.classList.remove('btn-outline-secondary');
                minusButton.classList.add('btn-outline-danger');
                minusButton.setAttribute('data-quantity', '1');
            } else {
                minusIcon.className = 'bi bi-dash';
                minusButton.classList.remove('btn-outline-danger');
                minusButton.classList.add('btn-outline-secondary');
                minusButton.setAttribute('data-quantity', quantity);
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });

    // Quantity input validation
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const min = parseInt(this.getAttribute('min')) || 0;
            const max = parseInt(this.getAttribute('max')) || Infinity;
            let value = parseInt(this.value) || 0;

            if (value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
                alert('Jumlah melebihi stok yang tersedia.');
            }
        });
    });

    // Search form enhancement
    const searchForm = document.querySelector('form[action*="products.php"]');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                // You can add real-time search suggestions here
                if (this.value.length >= 3) {
                    // Trigger search or show suggestions
                }
            });
        }
    }

    // Image lazy loading
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(function(img) {
        imageObserver.observe(img);
    });

    // Add to cart animation
    const addToCartForms = document.querySelectorAll('form[action*="cart.php"]');
    addToCartForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button) {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check-circle"></i> Ditambahkan!';
                button.classList.add('btn-success');
                button.classList.remove('btn-primary');

                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                }, 2000);
            }
        });
    });

    // Format price inputs
    const priceInputs = document.querySelectorAll('input[name="price"]');
    priceInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        });
    });

    // Smooth scroll to top
    const scrollToTopBtn = document.createElement('button');
    scrollToTopBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
    scrollToTopBtn.className = 'btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle d-none';
    scrollToTopBtn.style.width = '50px';
    scrollToTopBtn.style.height = '50px';
    scrollToTopBtn.style.zIndex = '1000';
    document.body.appendChild(scrollToTopBtn);

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.remove('d-none');
        } else {
            scrollToTopBtn.classList.add('d-none');
        }
    });

    scrollToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Form validation enhancement
    const forms = document.querySelectorAll('form[method="POST"]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang diperlukan.');
            }
        });
    });

    // Password strength indicator (for register form)
    const passwordInput = document.querySelector('input[name="password"]');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            let strengthText = '';
            let strengthClass = '';

            if (strength <= 2) {
                strengthText = 'Lemah';
                strengthClass = 'text-danger';
            } else if (strength <= 3) {
                strengthText = 'Sedang';
                strengthClass = 'text-warning';
            } else {
                strengthText = 'Kuat';
                strengthClass = 'text-success';
            }

            let indicator = this.nextElementSibling;
            if (!indicator || !indicator.classList.contains('password-strength')) {
                indicator = document.createElement('small');
                indicator.className = 'password-strength d-block mt-1';
                this.parentNode.insertBefore(indicator, this.nextSibling);
            }

            indicator.innerHTML = `Kekuatan Password: <span class="${strengthClass}">${strengthText}</span>`;
        });
    }

    // Image preview for file uploads
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = input.nextElementSibling;
                    if (!preview || !preview.classList.contains('image-preview')) {
                        preview = document.createElement('img');
                        preview.className = 'image-preview img-thumbnail mt-2';
                        preview.style.maxWidth = '200px';
                        input.parentNode.insertBefore(preview, input.nextSibling);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Tooltip initialization
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Cart update on quantity change - trigger AJAX submit
    const cartQuantityInputs = document.querySelectorAll('input[name="quantity"]');
    cartQuantityInputs.forEach(function(input) {
        let timeout;
        input.addEventListener('change', function() {
            clearTimeout(timeout);
            const form = this.closest('form');
            if (form && form.querySelector('input[name="action"][value="update"]')) {
                // Auto-submit via AJAX after a short delay
                timeout = setTimeout(function() {
                    const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                    form.dispatchEvent(submitEvent);
                }, 500);
            }
        });
    });

    // AJAX Add to Cart - Direct form handler
    console.log('Setting up AJAX cart handlers...'); // Debug
    
    const cartForms = document.querySelectorAll('form[action*="cart.php"], form[action=""]');
    console.log('Found cart forms:', cartForms.length); // Debug
    
    cartForms.forEach(function(form) {
        const actionInput = form.querySelector('input[name="action"]');
        if (actionInput) {
            const action = actionInput.value;
            console.log('Adding AJAX handler for action:', action); // Debug
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Konfirmasi untuk aksi remove
                if (action === 'remove') {
                    if (!confirm('Hapus produk ini dari keranjang?')) {
                        return false;
                    }
                }
                
                console.log('AJAX form submission triggered for action:', action); // Debug
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    if (action === 'add') {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menambahkan...';
                    }
                }
                
                // Build API URL
                const currentUrl = window.location.href;
                const baseUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
                const apiUrl = baseUrl + '/api/cart_handler.php';
                
                console.log('Sending to:', apiUrl); // Debug
                
                fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Show toast only for add action
                        if (action === 'add') {
                            showToast(data.message, 'success');
                        }
                        
                        // Update cart badge
                        if (data.cart_count !== undefined) {
                            updateCartBadge(data.cart_count);
                        }
                        
                        // Handle specific actions
                        if (action === 'update' && data.subtotal !== undefined) {
                            // Update subtotal in the card
                            const card = form.closest('.cart-item-card');
                            if (card) {
                                const subtotalEl = card.querySelector('.item-subtotal');
                                if (subtotalEl) {
                                    subtotalEl.textContent = formatRupiah(data.subtotal);
                                }
                            }
                            
                            // Update minus button icon after quantity change
                            updateMinusButtons();
                            
                            // Update totals
                            if (data.total !== undefined) {
                                const totalElement = document.querySelector('#cart-total');
                                const subtotalElement = document.querySelector('#cart-subtotal');
                                if (totalElement) {
                                    totalElement.textContent = formatRupiah(data.total);
                                }
                                if (subtotalElement) {
                                    subtotalElement.textContent = formatRupiah(data.total);
                                }
                            }
                        }
                        
                        if (action === 'remove') {
                            // Remove the card
                            const card = form.closest('.cart-item-card');
                            if (card) {
                                card.style.transition = 'opacity 0.3s';
                                card.style.opacity = '0';
                                setTimeout(() => {
                                    card.remove();
                                    
                                    // Check if cart is empty
                                    const cartContainer = document.querySelector('.col-lg-8');
                                    if (cartContainer && cartContainer.querySelectorAll('.cart-item-card').length === 0) {
                                        location.reload(); // Reload to show empty cart message
                                    }
                                }, 300);
                            }
                            // Update totals
                            if (data.total !== undefined) {
                                const totalElement = document.querySelector('#cart-total');
                                const subtotalElement = document.querySelector('#cart-subtotal');
                                if (totalElement) {
                                    totalElement.textContent = formatRupiah(data.total);
                                }
                                if (subtotalElement) {
                                    subtotalElement.textContent = formatRupiah(data.total);
                                }
                            }
                        }
                    } else {
                        showToast(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    showToast('Terjadi kesalahan: ' + error.message, 'danger');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                });
                
                return false;
            });
        }
    });

});

// Utility functions
function formatRupiah(number) {
    return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function truncateText(text, length) {
    if (text.length <= length) {
        return text;
    }
    return text.substring(0, length) + '...';
}

// Initialize minus buttons on cart page load
if (document.querySelector('.cart-update-form')) {
    setTimeout(() => {
        updateMinusButtons();
    }, 100);
}

// Product Card Click Handler - Event Delegation
document.addEventListener('click', function(e) {
    const productCard = e.target.closest('.product-clickable');
    
    if (!productCard) return;
    
    // Jika klik pada button, link, atau form, biarkan event default
    if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) {
        return;
    }
    
    // Cari link detail produk dan redirect
    const detailLink = productCard.querySelector('a[href*="product_detail.php"]');
    if (detailLink) {
        e.preventDefault();
        e.stopPropagation();
        window.location.href = detailLink.href;
    }
}, false);

// AJAX Login Handler
const loginForm = document.querySelector('form[action*="login.php"]');
if (loginForm && !loginForm.classList.contains('ajax-handled')) {
    loginForm.classList.add('ajax-handled');
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'login');
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Login...';
        
        fetch('api/auth_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                showToast(data.message, 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat login', 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
}

// AJAX Register Handler
const registerForm = document.querySelector('form[action*="register.php"]');
if (registerForm && !registerForm.classList.contains('ajax-handled')) {
    registerForm.classList.add('ajax-handled');
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'register');
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mendaftar...';
        
        fetch('api/auth_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                showToast(data.message, 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat registrasi', 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
}

// Console welcome message
console.log('%c TechShop E-Commerce ', 'background: #0d6efd; color: white; font-size: 20px; padding: 10px;');
console.log('%c Prototype Mode - PHP Native ', 'background: #ffc107; color: black; font-size: 14px; padding: 5px;');
console.log('%c AJAX Mode Enabled - No Page Reload! ', 'background: #198754; color: white; font-size: 12px; padding: 5px;');
