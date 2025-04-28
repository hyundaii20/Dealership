/**
 * Cookie consent management script
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get the cookie consent banner element
    const cookieConsentBanner = document.getElementById('cookie-consent-banner');
    
    // If banner exists and accept button exists
    if (cookieConsentBanner && document.getElementById('accept-cookies')) {
        // Add click event listener to the accept button
        document.getElementById('accept-cookies').addEventListener('click', function() {
            // Set cookie consent cookie for one year
            document.cookie = "cookie_consent=accepted; max-age=" + (86400 * 365) + "; path=/; SameSite=Lax";
            
            // Hide the banner
            cookieConsentBanner.style.display = "none";
        });
    }
}); 