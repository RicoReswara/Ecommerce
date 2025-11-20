<!-- Footer -->
    <footer class="mt-5 py-4 border-top">
        <div class="container-fluid">
            <div class="text-center text-muted">
                <p class="mb-0">&copy; 2025 TechShop Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Admin JS -->
<script>
    // Sidebar Toggle for Mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        if (sidebarToggle && sidebar && sidebarOverlay) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
            
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
            
            // Close sidebar when clicking nav link on mobile
            const sidebarLinks = sidebar.querySelectorAll('.nav-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                });
            });
        }
    });

    // Toast notification function
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

    // Auto dismiss alerts after 5 seconds
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // AJAX Delete Product
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete-product')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-delete-product');
            const productId = btn.getAttribute('data-id');
            const productName = btn.getAttribute('data-name');
            
            if (!confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"?`)) {
                return;
            }
            
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            const formData = new FormData();
            formData.append('action', 'delete_product');
            formData.append('id', productId);
            
            fetch('../api/admin_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Remove row with animation
                    const row = btn.closest('tr');
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                } else {
                    showToast(data.message, 'danger');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus produk', 'danger');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        }
        
        // AJAX Delete User
        if (e.target.closest('.btn-delete-user')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-delete-user');
            const userId = btn.getAttribute('data-id');
            const userName = btn.getAttribute('data-name');
            
            if (!confirm(`Apakah Anda yakin ingin menghapus user "${userName}"?`)) {
                return;
            }
            
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            const formData = new FormData();
            formData.append('action', 'delete_user');
            formData.append('id', userId);
            
            fetch('../api/admin_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    const row = btn.closest('tr');
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                } else {
                    showToast(data.message, 'danger');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus user', 'danger');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        }
        
        // AJAX Delete Category
        if (e.target.closest('.btn-delete-category')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-delete-category');
            const categoryId = btn.getAttribute('data-id');
            const categoryName = btn.getAttribute('data-name');
            
            if (!confirm(`Apakah Anda yakin ingin menghapus kategori "${categoryName}"?`)) {
                return;
            }
            
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            const formData = new FormData();
            formData.append('action', 'delete_category');
            formData.append('id', categoryId);
            
            fetch('../api/admin_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    const row = btn.closest('tr');
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                } else {
                    showToast(data.message, 'danger');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus kategori', 'danger');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        }
    });
    
    // AJAX Update Order Status
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('order-status-select')) {
            const select = e.target;
            const orderId = select.getAttribute('data-order-id');
            const newStatus = select.value;
            
            const formData = new FormData();
            formData.append('action', 'update_order_status');
            formData.append('id', orderId);
            formData.append('status', newStatus);
            
            select.disabled = true;
            
            fetch('../api/admin_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Update badge color based on status
                    const row = select.closest('tr');
                    const statusBadge = row.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.className = 'badge status-badge';
                        switch(newStatus) {
                            case 'pending':
                                statusBadge.classList.add('bg-warning');
                                statusBadge.textContent = 'Pending';
                                break;
                            case 'processing':
                                statusBadge.classList.add('bg-info');
                                statusBadge.textContent = 'Processing';
                                break;
                            case 'shipped':
                                statusBadge.classList.add('bg-primary');
                                statusBadge.textContent = 'Shipped';
                                break;
                            case 'delivered':
                                statusBadge.classList.add('bg-success');
                                statusBadge.textContent = 'Delivered';
                                break;
                            case 'cancelled':
                                statusBadge.classList.add('bg-danger');
                                statusBadge.textContent = 'Cancelled';
                                break;
                        }
                    }
                } else {
                    showToast(data.message, 'danger');
                }
                select.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat mengupdate status', 'danger');
                select.disabled = false;
            });
        }
    });
</script>

</body>
</html>
