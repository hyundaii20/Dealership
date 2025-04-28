<?php
// Cookie Manager for handling cookies across the website

// Set a cookie with proper defaults
function set_site_cookie($name, $value, $expire_days = 30) {
    $expire = time() + (86400 * $expire_days); // 86400 = 1 day in seconds
    setcookie($name, $value, $expire, "/", "", isset($_SERVER["HTTPS"]), true);
}

// Get a cookie value, with optional default if not set
function get_site_cookie($name, $default = null) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

// Delete a cookie
function delete_site_cookie($name) {
    if (isset($_COOKIE[$name])) {
        setcookie($name, "", time() - 3600, "/", "", isset($_SERVER["HTTPS"]), true);
    }
}

// Check if a cookie exists
function cookie_exists($name) {
    return isset($_COOKIE[$name]);
}

// Create a cookie consent banner (returns HTML)
function get_cookie_consent_html() {
    if (!cookie_exists('cookie_consent')) {
        return '
        <div id="cookie-consent-banner">
            <p>This website uses cookies to ensure you get the best experience on our website. 
            <button id="accept-cookies">Accept</button></p>
        </div>';
    }
    return '';
}

// Remember the user's currency preference
function set_currency_preference($currency) {
    set_site_cookie('currency_preference', $currency, 90); // Remember for 90 days
}

// Get the user's preferred currency (default to EUR)
function get_currency_preference() {
    return get_site_cookie('currency_preference', 'EUR');
}

// Remember the last visited page
function set_last_visited_page($page) {
    set_site_cookie('last_visited_page', $page, 1); // Remember for 1 day
}

// Get the last visited page
function get_last_visited_page() {
    return get_site_cookie('last_visited_page', 'index.php');
}

// Remember items viewed by user
function add_viewed_car($car_id) {
    $viewed_cars = get_viewed_cars();
    
    // Check if car already in list
    if (!in_array($car_id, $viewed_cars)) {
        // Add to beginning of array and limit to 5 items
        array_unshift($viewed_cars, $car_id);
        $viewed_cars = array_slice($viewed_cars, 0, 5);
    }
    
    set_site_cookie('viewed_cars', json_encode($viewed_cars), 7);
}

// Get viewed cars
function get_viewed_cars() {
    $cookie_value = get_site_cookie('viewed_cars', '[]');
    $viewed_cars = json_decode($cookie_value, true);
    
    // Ensure it's an array
    if (!is_array($viewed_cars)) {
        $viewed_cars = [];
    }
    
    return $viewed_cars;
}

// Set theme preference (light/dark)
function set_theme_preference($theme) {
    set_site_cookie('theme_preference', $theme, 365); // Remember for a year
}

// Get theme preference (default to light)
function get_theme_preference() {
    return get_site_cookie('theme_preference', 'light');
}

/**
 * Add a car to recently viewed cars in cookies
 * 
 * @param int $car_id The ID of the car being viewed
 * @return void
 */
function add_to_recently_viewed($car_id) {
    // Get current recently viewed cars
    $recently_viewed = get_site_cookie('recently_viewed');
    $recently_viewed_array = [];
    
    // Parse existing cookie if it exists
    if ($recently_viewed) {
        $recently_viewed_array = json_decode($recently_viewed, true);
    }
    
    // Remove the car if it's already in the list (to add it to the front)
    $recently_viewed_array = array_filter($recently_viewed_array, function($id) use ($car_id) {
        return $id != $car_id;
    });
    
    // Add the current car ID to the front of the array
    array_unshift($recently_viewed_array, $car_id);
    
    // Keep only the most recent 5 cars
    $recently_viewed_array = array_slice($recently_viewed_array, 0, 5);
    
    // Save the updated recently viewed cars
    set_site_cookie('recently_viewed', json_encode($recently_viewed_array), time() + (86400 * 30)); // 30 days
}

/**
 * Get list of recently viewed car IDs
 * 
 * @return array Array of recently viewed car IDs
 */
function get_recently_viewed_cars() {
    $recently_viewed = get_site_cookie('recently_viewed');
    
    if ($recently_viewed) {
        return json_decode($recently_viewed, true);
    }
    
    return [];
}
?> 