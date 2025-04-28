/**
 * Checkout form validation and functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get the checkout form
    const checkoutForm = document.getElementById('checkout-form');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Get all required fields
            const requiredFields = checkoutForm.querySelectorAll('[required]');
            let isValid = true;
            
            // Check each required field
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    // If field is empty, mark it as invalid
                    field.classList.add('is-invalid');
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    // If field is filled, mark it as valid
                    field.classList.remove('is-invalid');
                    field.style.borderColor = '#28a745';
                }
            });
            
            // If form is not valid, prevent submission
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
            
            // Get selected payment method
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            
            // Check if payment method is selected
            if (!paymentMethod) {
                e.preventDefault();
                alert('Please select a payment method');
            }
        });
        
        // Add event listener to required fields to validate on change
        const requiredFields = checkoutForm.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            field.addEventListener('change', function() {
                if (!this.value.trim()) {
                    this.classList.add('is-invalid');
                    this.style.borderColor = '#dc3545';
                } else {
                    this.classList.remove('is-invalid');
                    this.style.borderColor = '#28a745';
                }
            });
        });
    }
}); 