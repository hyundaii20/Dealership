/**
 * Cart page functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get all quantity inputs
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    // Add event listeners to each quantity input
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            // If quantity is less than 0, set it to 0
            if (this.value < 0) {
                this.value = 0;
            }
            
            // You could add auto-update functionality here if needed
            // For now, user needs to click "Update Cart" button
        });
    });
    
    // Get all remove buttons
    const removeButtons = document.querySelectorAll('.remove-btn');
    
    // Add confirmation to remove buttons
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                e.preventDefault();
            }
        });
    });
}); 