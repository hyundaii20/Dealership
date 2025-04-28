<?php
// Script to add sample car data to the database

// Only run this script directly if not already included
if (!defined('SAMPLE_CARS_LOADED')) {
    define('SAMPLE_CARS_LOADED', true);
    
    // Include required files if not already included
    if (!isset($conn)) {
        require_once 'Database.php';
        
        // Initialize database connection
        $db = new Database();
        $conn = Database::getConnection();
    }
    
    // Clear existing cars from database if run directly
    if (!isset($cars_count)) {
        $truncate_result = $conn->query("TRUNCATE TABLE cars");
        echo "Cleared existing cars from database.<br>";
    }
}

// Sample car data with multiple models for each manufacturer
$cars = [
    // Toyota models
    [
        'make' => 'Toyota', 
        'model' => 'Camry', 
        'year' => 2022, 
        'color' => 'Silver', 
        'vin' => 'JT2BF22K1W0123456', 
        'price' => 25999.99,
        'description' => 'The Toyota Camry is a comfortable and reliable sedan with excellent fuel economy and a spacious interior.',
        'stock' => 5
    ],
    [
        'make' => 'Toyota', 
        'model' => 'Corolla', 
        'year' => 2023, 
        'color' => 'White', 
        'vin' => 'JT2BF22K1W0123457', 
        'price' => 21799.99,
        'description' => 'The Toyota Corolla is an economical compact car with great fuel efficiency and modern safety features.',
        'stock' => 7
    ],
    [
        'make' => 'Toyota', 
        'model' => 'RAV4', 
        'year' => 2022, 
        'color' => 'Blue', 
        'vin' => 'JT2BF22K1W0123458', 
        'price' => 27995.00,
        'description' => 'The Toyota RAV4 is a versatile compact SUV with ample cargo space and all-wheel drive capability.',
        'stock' => 4
    ],
    [
        'make' => 'Toyota', 
        'model' => 'Highlander', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => 'JT2BF22K1W0123459', 
        'price' => 35800.00,
        'description' => 'The Toyota Highlander is a spacious three-row SUV perfect for families, with available hybrid powertrain.',
        'stock' => 3
    ],
    
    // Honda models
    [
        'make' => 'Honda', 
        'model' => 'Civic', 
        'year' => 2023, 
        'color' => 'Blue', 
        'vin' => 'JHMEJ6674YS012345', 
        'price' => 23499.50,
        'description' => 'The Honda Civic offers a sporty driving experience with advanced safety features and great fuel efficiency.',
        'stock' => 5
    ],
    [
        'make' => 'Honda', 
        'model' => 'Accord', 
        'year' => 2022, 
        'color' => 'Silver', 
        'vin' => 'JHMEJ6674YS012346', 
        'price' => 26799.00,
        'description' => 'The Honda Accord is a midsize sedan with upscale features, responsive handling, and a comfortable interior.',
        'stock' => 4
    ],
    [
        'make' => 'Honda', 
        'model' => 'CR-V', 
        'year' => 2023, 
        'color' => 'Gray', 
        'vin' => 'JHMEJ6674YS012347', 
        'price' => 28500.00,
        'description' => 'The Honda CR-V is a compact SUV with excellent cargo space, fuel economy, and reliability.',
        'stock' => 6
    ],
    [
        'make' => 'Honda', 
        'model' => 'Pilot', 
        'year' => 2022, 
        'color' => 'Black', 
        'vin' => 'JHMEJ6674YS012348', 
        'price' => 38750.00,
        'description' => 'The Honda Pilot is a three-row SUV with abundant passenger space and towing capability.',
        'stock' => 3
    ],
    
    // Ford models
    [
        'make' => 'Ford', 
        'model' => 'Mustang', 
        'year' => 2021, 
        'color' => 'Red', 
        'vin' => '1ZVBP8AM4C5234567', 
        'price' => 42750.00,
        'description' => 'The iconic Ford Mustang delivers powerful performance with its V8 engine and classic American muscle car design.',
        'stock' => 2
    ],
    [
        'make' => 'Ford', 
        'model' => 'F-150', 
        'year' => 2022, 
        'color' => 'Blue', 
        'vin' => '1ZVBP8AM4C5234568', 
        'price' => 39995.00,
        'description' => 'The Ford F-150 is America\'s best-selling pickup truck with exceptional towing capacity and innovative features.',
        'stock' => 5
    ],
    [
        'make' => 'Ford', 
        'model' => 'Explorer', 
        'year' => 2023, 
        'color' => 'White', 
        'vin' => '1ZVBP8AM4C5234569', 
        'price' => 36500.00,
        'description' => 'The Ford Explorer is a versatile three-row SUV with powerful engine options and spacious interior.',
        'stock' => 4
    ],
    [
        'make' => 'Ford', 
        'model' => 'Escape', 
        'year' => 2022, 
        'color' => 'Silver', 
        'vin' => '1ZVBP8AM4C5234570', 
        'price' => 28450.00,
        'description' => 'The Ford Escape is a compact SUV with agile handling and a range of available powertrains including hybrid options.',
        'stock' => 6
    ],
    
    // BMW models
    [
        'make' => 'BMW', 
        'model' => '3 Series', 
        'year' => 2022, 
        'color' => 'Black', 
        'vin' => 'WBA3B3C52EF345678', 
        'price' => 43300.00,
        'description' => 'The BMW 3 Series combines luxury with performance, offering precise handling and a premium interior.',
        'stock' => 3
    ],
    [
        'make' => 'BMW', 
        'model' => '5 Series', 
        'year' => 2023, 
        'color' => 'Gray', 
        'vin' => 'WBA3B3C52EF345679', 
        'price' => 54200.00,
        'description' => 'The BMW 5 Series is a midsize luxury sedan with cutting-edge technology and refined driving dynamics.',
        'stock' => 2
    ],
    [
        'make' => 'BMW', 
        'model' => 'X3', 
        'year' => 2022, 
        'color' => 'Blue', 
        'vin' => 'WBA3B3C52EF345680', 
        'price' => 45700.00,
        'description' => 'The BMW X3 is a compact luxury SUV that balances sporty performance with practical utility.',
        'stock' => 4
    ],
    [
        'make' => 'BMW', 
        'model' => 'X5', 
        'year' => 2023, 
        'color' => 'White', 
        'vin' => 'WBA3B3C52EF345681', 
        'price' => 63900.00,
        'description' => 'The BMW X5 is a midsize luxury SUV with powerful engines, advanced technology, and upscale interior.',
        'stock' => 2
    ],
    [
        'make' => 'BMW', 
        'model' => 'X7', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => 'WBA3B3C52EF345682', 
        'price' => 77850.00,
        'description' => 'The BMW X7 is a full-size luxury SUV with three rows of seating, powerful engines, and premium features.',
        'stock' => 2
    ],
    [
        'make' => 'BMW', 
        'model' => '7 Series', 
        'year' => 2023, 
        'color' => 'Silver', 
        'vin' => 'WBA3B3C52EF345683', 
        'price' => 93400.00,
        'description' => 'The BMW 7 Series is a full-size luxury sedan with cutting-edge technology and exceptional comfort.',
        'stock' => 1
    ],
    [
        'make' => 'BMW', 
        'model' => 'M3', 
        'year' => 2023, 
        'color' => 'Blue', 
        'vin' => 'WBA3B3C52EF345684', 
        'price' => 72800.00,
        'description' => 'The BMW M3 is a high-performance sports sedan with track-ready capabilities and aggressive styling.',
        'stock' => 2
    ],
    [
        'make' => 'BMW', 
        'model' => 'M5', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => 'WBA3B3C52EF345685', 
        'price' => 106700.00,
        'description' => 'The BMW M5 is a high-performance luxury sedan with a twin-turbo V8 engine and sophisticated all-wheel drive.',
        'stock' => 1
    ],
    
    // Chevrolet models
    [
        'make' => 'Chevrolet', 
        'model' => 'Silverado', 
        'year' => 2021, 
        'color' => 'Gray', 
        'vin' => '3GCNWAEF9LG456789', 
        'price' => 38795.00,
        'description' => 'The Chevrolet Silverado is a rugged pickup truck with impressive towing capacity and a comfortable cabin.',
        'stock' => 4
    ],
    [
        'make' => 'Chevrolet', 
        'model' => 'Equinox', 
        'year' => 2022, 
        'color' => 'Silver', 
        'vin' => '3GCNWAEF9LG456790', 
        'price' => 27500.00,
        'description' => 'The Chevrolet Equinox is a compact SUV with good fuel economy and comfortable ride quality.',
        'stock' => 5
    ],
    [
        'make' => 'Chevrolet', 
        'model' => 'Tahoe', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => '3GCNWAEF9LG456791', 
        'price' => 54200.00,
        'description' => 'The Chevrolet Tahoe is a full-size SUV with seating for up to eight passengers and strong towing capability.',
        'stock' => 3
    ],
    [
        'make' => 'Chevrolet', 
        'model' => 'Camaro', 
        'year' => 2022, 
        'color' => 'Red', 
        'vin' => '3GCNWAEF9LG456792', 
        'price' => 38900.00,
        'description' => 'The Chevrolet Camaro is a thrilling sports car with powerful engine options and sharp handling.',
        'stock' => 2
    ],
    
    // Additional brands
    [
        'make' => 'Tesla', 
        'model' => 'Model 3', 
        'year' => 2023, 
        'color' => 'White', 
        'vin' => '5YJ3E1EA1PF123456', 
        'price' => 46990.00,
        'description' => 'The Tesla Model 3 is a fully electric vehicle with cutting-edge technology, autopilot capabilities, and zero emissions.',
        'stock' => 4
    ],
    [
        'make' => 'Tesla', 
        'model' => 'Model Y', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => '5YJ3E1EA1PF123457', 
        'price' => 54990.00,
        'description' => 'The Tesla Model Y is an all-electric compact SUV with long range capabilities and Tesla\'s innovative features.',
        'stock' => 3
    ],
    [
        'make' => 'Jeep', 
        'model' => 'Wrangler', 
        'year' => 2023, 
        'color' => 'Green', 
        'vin' => '1C4HJXDG1MW567890', 
        'price' => 33995.00,
        'description' => 'The Jeep Wrangler is designed for off-road adventures with its 4-wheel drive system and removable top.',
        'stock' => 4
    ],
    [
        'make' => 'Jeep', 
        'model' => 'Grand Cherokee', 
        'year' => 2022, 
        'color' => 'Black', 
        'vin' => '1C4HJXDG1MW567891', 
        'price' => 41995.00,
        'description' => 'The Jeep Grand Cherokee combines luxury with off-road capability in a midsize SUV package.',
        'stock' => 3
    ],
    [
        'make' => 'Nissan', 
        'model' => 'Altima', 
        'year' => 2022, 
        'color' => 'Silver', 
        'vin' => '1N4BL4EV2KC678901', 
        'price' => 24550.00,
        'description' => 'The Nissan Altima offers a smooth ride with innovative safety features and good fuel economy.',
        'stock' => 5
    ],
    [
        'make' => 'Nissan', 
        'model' => 'Rogue', 
        'year' => 2023, 
        'color' => 'Blue', 
        'vin' => '1N4BL4EV2KC678902', 
        'price' => 27500.00,
        'description' => 'The Nissan Rogue is a compact SUV with upscale interior materials and comfortable seating.',
        'stock' => 4
    ],
    [
        'make' => 'Audi', 
        'model' => 'A4', 
        'year' => 2023, 
        'color' => 'Blue', 
        'vin' => 'WAUAFAFL1EN789012', 
        'price' => 40995.00,
        'description' => 'The Audi A4 is a luxury sedan with elegant styling, advanced technology, and a refined driving experience.',
        'stock' => 3
    ],
    [
        'make' => 'Audi', 
        'model' => 'Q5', 
        'year' => 2022, 
        'color' => 'Gray', 
        'vin' => 'WAUAFAFL1EN789013', 
        'price' => 45800.00,
        'description' => 'The Audi Q5 is a compact luxury SUV with a premium interior and standard all-wheel drive.',
        'stock' => 4
    ],
    [
        'make' => 'Subaru', 
        'model' => 'Outback', 
        'year' => 2022, 
        'color' => 'Green', 
        'vin' => '4S4BSACC5N3890123', 
        'price' => 27645.00,
        'description' => 'The Subaru Outback is a versatile crossover with standard all-wheel drive and impressive cargo space.',
        'stock' => 5
    ],
    [
        'make' => 'Subaru', 
        'model' => 'Forester', 
        'year' => 2023, 
        'color' => 'Silver', 
        'vin' => '4S4BSACC5N3890124', 
        'price' => 25895.00,
        'description' => 'The Subaru Forester is a compact SUV with excellent visibility, standard all-wheel drive, and a spacious interior.',
        'stock' => 4
    ],
    // Additional brands
    [
        'make' => 'Mercedes-Benz', 
        'model' => 'C-Class', 
        'year' => 2023, 
        'color' => 'Silver', 
        'vin' => 'WDDWF4KB2KR123456', 
        'price' => 43900.00,
        'description' => 'The Mercedes-Benz C-Class offers luxury, performance, and cutting-edge technology in a compact executive sedan.',
        'stock' => 3
    ],
    [
        'make' => 'Mercedes-Benz', 
        'model' => 'E-Class', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => 'WDDWF4KB2KR123457', 
        'price' => 56750.00,
        'description' => 'The Mercedes-Benz E-Class combines sophisticated design with advanced safety features and premium comfort.',
        'stock' => 2
    ],
    [
        'make' => 'Mercedes-Benz', 
        'model' => 'S-Class', 
        'year' => 2023, 
        'color' => 'White', 
        'vin' => 'WDDWF4KB2KR123458', 
        'price' => 114700.00,
        'description' => 'The Mercedes-Benz S-Class represents the pinnacle of luxury with innovative technology and exceptional ride quality.',
        'stock' => 1
    ],
    [
        'make' => 'Mercedes-Benz', 
        'model' => 'GLC', 
        'year' => 2023, 
        'color' => 'Blue', 
        'vin' => 'WDDWF4KB2KR123459', 
        'price' => 47100.00,
        'description' => 'The Mercedes-Benz GLC is a compact luxury SUV with elegant styling, refined handling, and premium interior.',
        'stock' => 3
    ],
    [
        'make' => 'Mercedes-Benz', 
        'model' => 'GLE', 
        'year' => 2022, 
        'color' => 'Gray', 
        'vin' => 'WDDWF4KB2KR123460', 
        'price' => 63600.00,
        'description' => 'The Mercedes-Benz GLE is a midsize luxury SUV with powerful engines, advanced technology, and spacious interior.',
        'stock' => 2
    ],
    [
        'make' => 'Audi', 
        'model' => 'A6', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => 'WAUAFAFL1EN789014', 
        'price' => 56900.00,
        'description' => 'The Audi A6 is a sophisticated midsize luxury sedan with innovative technology and refined comfort.',
        'stock' => 2
    ],
    [
        'make' => 'Audi', 
        'model' => 'Q7', 
        'year' => 2023, 
        'color' => 'White', 
        'vin' => 'WAUAFAFL1EN789015', 
        'price' => 58200.00,
        'description' => 'The Audi Q7 is a luxurious three-row SUV with a premium interior and advanced driver assistance features.',
        'stock' => 3
    ],
    [
        'make' => 'Audi', 
        'model' => 'e-tron', 
        'year' => 2023, 
        'color' => 'Silver', 
        'vin' => 'WAUAFAFL1EN789016', 
        'price' => 70800.00,
        'description' => 'The Audi e-tron is a fully electric luxury SUV with impressive range and cutting-edge technology.',
        'stock' => 2
    ],
    [
        'make' => 'Audi', 
        'model' => 'S4', 
        'year' => 2023, 
        'color' => 'Red', 
        'vin' => 'WAUAFAFL1EN789017', 
        'price' => 52800.00,
        'description' => 'The Audi S4 is a high-performance luxury sedan with sports-tuned suspension and powerful turbocharged engine.',
        'stock' => 2
    ],
    [
        'make' => 'Lexus', 
        'model' => 'ES', 
        'year' => 2023, 
        'color' => 'Silver', 
        'vin' => 'JTHBK1GG7D2123456', 
        'price' => 42590.00,
        'description' => 'The Lexus ES combines luxury and reliability with a smooth ride and well-appointed interior.',
        'stock' => 3
    ],
    [
        'make' => 'Lexus', 
        'model' => 'RX', 
        'year' => 2023, 
        'color' => 'White', 
        'vin' => 'JTHBK1GG7D2123457', 
        'price' => 48550.00,
        'description' => 'The Lexus RX is a luxury midsize SUV known for its refined ride, reliability, and upscale features.',
        'stock' => 4
    ],
    [
        'make' => 'Lexus', 
        'model' => 'LS', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => 'JTHBK1GG7D2123458', 
        'price' => 77250.00,
        'description' => 'The Lexus LS is a full-size luxury sedan with exceptional comfort, advanced technology, and meticulous craftsmanship.',
        'stock' => 2
    ],
    [
        'make' => 'Lexus', 
        'model' => 'NX', 
        'year' => 2023, 
        'color' => 'Gray', 
        'vin' => 'JTHBK1GG7D2123459', 
        'price' => 39500.00,
        'description' => 'The Lexus NX is a compact luxury SUV with bold styling, comfortable interior, and available hybrid powertrain.',
        'stock' => 3
    ],
    [
        'make' => 'Volkswagen', 
        'model' => 'Golf', 
        'year' => 2023, 
        'color' => 'Blue', 
        'vin' => 'WVWAA71K48W234567', 
        'price' => 25095.00,
        'description' => 'The Volkswagen Golf is a versatile hatchback with refined driving dynamics and a well-designed interior.',
        'stock' => 4
    ],
    [
        'make' => 'Volkswagen', 
        'model' => 'Tiguan', 
        'year' => 2023, 
        'color' => 'Gray', 
        'vin' => 'WVWAA71K48W234568', 
        'price' => 27495.00,
        'description' => 'The Volkswagen Tiguan is a compact SUV with European styling, comfortable ride, and flexible seating options.',
        'stock' => 5
    ],
    [
        'make' => 'Volkswagen', 
        'model' => 'Passat', 
        'year' => 2022, 
        'color' => 'Silver', 
        'vin' => 'WVWAA71K48W234569', 
        'price' => 27575.00,
        'description' => 'The Volkswagen Passat is a spacious midsize sedan with comfortable ride quality and understated elegance.',
        'stock' => 3
    ],
    [
        'make' => 'Volkswagen', 
        'model' => 'Atlas', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => 'WVWAA71K48W234570', 
        'price' => 35150.00,
        'description' => 'The Volkswagen Atlas is a three-row SUV with generous passenger and cargo space, designed for family versatility.',
        'stock' => 3
    ],
    // Hyundai models
    [
        'make' => 'Hyundai', 
        'model' => 'Tucson', 
        'year' => 2023, 
        'color' => 'Silver', 
        'vin' => 'KM8J3CAL4NU345678', 
        'price' => 27700.00,
        'description' => 'The Hyundai Tucson is a stylish compact SUV with advanced safety features and an upscale interior design.',
        'stock' => 5
    ],
    [
        'make' => 'Hyundai', 
        'model' => 'Elantra', 
        'year' => 2023, 
        'color' => 'Blue', 
        'vin' => 'KM8J3CAL4NU345679', 
        'price' => 21950.00,
        'description' => 'The Hyundai Elantra offers excellent fuel economy, distinctive styling, and a comfortable ride at an affordable price.',
        'stock' => 6
    ],
    [
        'make' => 'Hyundai', 
        'model' => 'Santa Fe', 
        'year' => 2022, 
        'color' => 'White', 
        'vin' => 'KM8J3CAL4NU345680', 
        'price' => 29000.00,
        'description' => 'The Hyundai Santa Fe is a midsize SUV with room for five passengers, offering a premium interior and smooth ride quality.',
        'stock' => 4
    ],
    [
        'make' => 'Hyundai', 
        'model' => 'Palisade', 
        'year' => 2023, 
        'color' => 'Black', 
        'vin' => 'KM8J3CAL4NU345681', 
        'price' => 35200.00,
        'description' => 'The Hyundai Palisade is a three-row SUV with upscale design, premium features, and comfortable seating for up to eight passengers.',
        'stock' => 3
    ],
    // Kia models
    [
        'make' => 'Kia', 
        'model' => 'Telluride', 
        'year' => 2023, 
        'color' => 'Dark Moss', 
        'vin' => '5XYP3DHC9PG456789', 
        'price' => 35890.00,
        'description' => 'The Kia Telluride is an award-winning midsize SUV with three rows of seating, rugged styling, and premium features.',
        'stock' => 4
    ],
    [
        'make' => 'Kia', 
        'model' => 'Sportage', 
        'year' => 2023, 
        'color' => 'Gray', 
        'vin' => '5XYP3DHC9PG456790', 
        'price' => 27290.00,
        'description' => 'The Kia Sportage offers bold styling, a spacious interior, and excellent value with its extensive feature set.',
        'stock' => 5
    ],
    [
        'make' => 'Kia', 
        'model' => 'Sorento', 
        'year' => 2022, 
        'color' => 'Silver', 
        'vin' => '5XYP3DHC9PG456791', 
        'price' => 31090.00,
        'description' => 'The Kia Sorento is a versatile midsize SUV with available third-row seating and hybrid powertrain options.',
        'stock' => 3
    ],
    [
        'make' => 'Kia', 
        'model' => 'K5', 
        'year' => 2023, 
        'color' => 'Blue', 
        'vin' => '5XYP3DHC9PG456792', 
        'price' => 25290.00,
        'description' => 'The Kia K5 is a stylish midsize sedan with sporty handling, advanced technology, and attractive design inside and out.',
        'stock' => 4
    ],
    // Mazda models
    [
        'make' => 'Mazda', 
        'model' => 'CX-5', 
        'year' => 2023, 
        'color' => 'Soul Red', 
        'vin' => 'JM3KE4DY4F0567890', 
        'price' => 28500.00,
        'description' => 'The Mazda CX-5 is a compact SUV with upscale styling, engaging driving dynamics, and premium interior quality.',
        'stock' => 5
    ],
    [
        'make' => 'Mazda', 
        'model' => 'CX-9', 
        'year' => 2022, 
        'color' => 'Machine Gray', 
        'vin' => 'JM3KE4DY4F0567891', 
        'price' => 36450.00,
        'description' => 'The Mazda CX-9 is a three-row SUV with an upscale interior, responsive handling, and sleek exterior design.',
        'stock' => 3
    ],
    [
        'make' => 'Mazda', 
        'model' => 'Mazda3', 
        'year' => 2023, 
        'color' => 'Deep Crystal Blue', 
        'vin' => 'JM3KE4DY4F0567892', 
        'price' => 23450.00,
        'description' => 'The Mazda3 is a compact car available in sedan and hatchback body styles, offering premium features and sporty handling.',
        'stock' => 4
    ],
    [
        'make' => 'Mazda', 
        'model' => 'MX-5 Miata', 
        'year' => 2023, 
        'color' => 'Red', 
        'vin' => 'JM3KE4DY4F0567893', 
        'price' => 28995.00,
        'description' => 'The Mazda MX-5 Miata is an iconic two-seat roadster with perfectly balanced handling and a convertible top for open-air driving.',
        'stock' => 2
    ],
    // Acura models
    [
        'make' => 'Acura', 
        'model' => 'MDX', 
        'year' => 2023, 
        'color' => 'Platinum White', 
        'vin' => '19UYA2289L0678901', 
        'price' => 49550.00,
        'description' => 'The Acura MDX is a three-row luxury SUV with sophisticated styling, powerful performance, and advanced technology features.',
        'stock' => 3
    ],
    [
        'make' => 'Acura', 
        'model' => 'RDX', 
        'year' => 2022, 
        'color' => 'Majestic Black', 
        'vin' => '19UYA2289L0678902', 
        'price' => 41800.00,
        'description' => 'The Acura RDX is a compact luxury SUV with sporty handling, a turbocharged engine, and a well-appointed interior.',
        'stock' => 4
    ],
    [
        'make' => 'Acura', 
        'model' => 'TLX', 
        'year' => 2023, 
        'color' => 'Silver', 
        'vin' => '19UYA2289L0678903', 
        'price' => 39650.00,
        'description' => 'The Acura TLX is a premium sport sedan with precise handling, available all-wheel drive, and elegant design.',
        'stock' => 3
    ],
    [
        'make' => 'Acura', 
        'model' => 'Integra', 
        'year' => 2023, 
        'color' => 'Apex Blue', 
        'vin' => '19UYA2289L0678904', 
        'price' => 31300.00,
        'description' => 'The all-new Acura Integra combines sporty performance with premium features in a versatile five-door liftback design.',
        'stock' => 4
    ],
    // Porsche models
    [
        'make' => 'Porsche', 
        'model' => '911', 
        'year' => 2023, 
        'color' => 'Guards Red', 
        'vin' => 'WP0AA2A94LS789012', 
        'price' => 106100.00,
        'description' => 'The Porsche 911 is an iconic sports car with exceptional performance, precise handling, and timeless design.',
        'stock' => 2
    ],
    [
        'make' => 'Porsche', 
        'model' => 'Cayenne', 
        'year' => 2023, 
        'color' => 'Moonlight Blue', 
        'vin' => 'WP0AA2A94LS789013', 
        'price' => 72200.00,
        'description' => 'The Porsche Cayenne combines sports car performance with SUV versatility, featuring powerful engines and luxurious comfort.',
        'stock' => 3
    ],
    [
        'make' => 'Porsche', 
        'model' => 'Macan', 
        'year' => 2023, 
        'color' => 'Carrara White', 
        'vin' => 'WP0AA2A94LS789014', 
        'price' => 57500.00,
        'description' => 'The Porsche Macan is a compact luxury SUV that delivers thrilling driving dynamics with everyday usability.',
        'stock' => 4
    ],
    [
        'make' => 'Porsche', 
        'model' => 'Taycan', 
        'year' => 2023, 
        'color' => 'Gentian Blue', 
        'vin' => 'WP0AA2A94LS789015', 
        'price' => 86700.00,
        'description' => 'The Porsche Taycan is an all-electric sports car with breathtaking acceleration, precise handling, and cutting-edge technology.',
        'stock' => 2
    ],
    // Land Rover models
    [
        'make' => 'Land Rover', 
        'model' => 'Range Rover', 
        'year' => 2023, 
        'color' => 'Santorini Black', 
        'vin' => 'SALGS2SF7PA890123', 
        'price' => 104500.00,
        'description' => 'The Range Rover is the pinnacle of luxury SUVs, combining sophisticated design, exceptional off-road capability, and refined comfort.',
        'stock' => 2
    ],
    [
        'make' => 'Land Rover', 
        'model' => 'Range Rover Sport', 
        'year' => 2023, 
        'color' => 'Firenze Red', 
        'vin' => 'SALGS2SF7PA890124', 
        'price' => 83000.00,
        'description' => 'The Range Rover Sport delivers dynamic performance both on and off-road, with luxurious accommodations and advanced technology.',
        'stock' => 3
    ],
    [
        'make' => 'Land Rover', 
        'model' => 'Defender', 
        'year' => 2023, 
        'color' => 'Pangea Green', 
        'vin' => 'SALGS2SF7PA890125', 
        'price' => 53500.00,
        'description' => 'The Land Rover Defender is an iconic off-road vehicle reimagined with modern technology while maintaining its legendary capability.',
        'stock' => 4
    ],
    [
        'make' => 'Land Rover', 
        'model' => 'Discovery', 
        'year' => 2023, 
        'color' => 'Fuji White', 
        'vin' => 'SALGS2SF7PA890126', 
        'price' => 56750.00,
        'description' => 'The Land Rover Discovery combines versatility and comfort with impressive off-road capability and seating for seven.',
        'stock' => 3
    ],
    // Volvo models
    [
        'make' => 'Volvo', 
        'model' => 'XC90', 
        'year' => 2023, 
        'color' => 'Denim Blue', 
        'vin' => 'YV4A22PK4F1901234', 
        'price' => 56000.00,
        'description' => 'The Volvo XC90 is a luxury three-row SUV known for its Scandinavian design, innovative safety features, and refined comfort.',
        'stock' => 3
    ],
    [
        'make' => 'Volvo', 
        'model' => 'XC60', 
        'year' => 2023, 
        'color' => 'Crystal White', 
        'vin' => 'YV4A22PK4F1901235', 
        'price' => 43450.00,
        'description' => 'The Volvo XC60 is a midsize luxury SUV with elegant styling, advanced safety technology, and a serene interior.',
        'stock' => 4
    ],
    [
        'make' => 'Volvo', 
        'model' => 'S60', 
        'year' => 2023, 
        'color' => 'Onyx Black', 
        'vin' => 'YV4A22PK4F1901236', 
        'price' => 41300.00,
        'description' => 'The Volvo S60 is a sophisticated luxury sedan with distinctive design, comfortable ride, and comprehensive safety features.',
        'stock' => 3
    ],
    [
        'make' => 'Volvo', 
        'model' => 'V60', 
        'year' => 2023, 
        'color' => 'Pine Gray', 
        'vin' => 'YV4A22PK4F1901237', 
        'price' => 45900.00,
        'description' => 'The Volvo V60 is a premium wagon that combines practicality with elegant design and a refined driving experience.',
        'stock' => 2
    ],
    // Cadillac models
    [
        'make' => 'Cadillac', 
        'model' => 'Escalade', 
        'year' => 2023, 
        'color' => 'Black Raven', 
        'vin' => '1GYS4DKL9PR012345', 
        'price' => 79795.00,
        'description' => 'The Cadillac Escalade is a full-size luxury SUV with commanding presence, opulent interior, and cutting-edge technology.',
        'stock' => 3
    ],
    [
        'make' => 'Cadillac', 
        'model' => 'CT5', 
        'year' => 2023, 
        'color' => 'Crystal White', 
        'vin' => '1GYS4DKL9PR012346', 
        'price' => 38695.00,
        'description' => 'The Cadillac CT5 is a luxury sedan with athletic handling, refined comfort, and a driver-focused experience.',
        'stock' => 4
    ],
    [
        'make' => 'Cadillac', 
        'model' => 'XT5', 
        'year' => 2023, 
        'color' => 'Satin Steel', 
        'vin' => '1GYS4DKL9PR012347', 
        'price' => 44195.00,
        'description' => 'The Cadillac XT5 is a midsize luxury crossover offering sophisticated styling, premium comfort, and advanced safety features.',
        'stock' => 5
    ],
    [
        'make' => 'Cadillac', 
        'model' => 'XT4', 
        'year' => 2023, 
        'color' => 'Stellar Black', 
        'vin' => '1GYS4DKL9PR012348', 
        'price' => 36295.00,
        'description' => 'The Cadillac XT4 is a compact luxury SUV with bold design, agile handling, and a tech-forward interior.',
        'stock' => 4
    ],
    // Infiniti models
    [
        'make' => 'Infiniti', 
        'model' => 'QX80', 
        'year' => 2023, 
        'color' => 'Graphite Shadow', 
        'vin' => 'JN8AZ2NE3M9123456', 
        'price' => 72250.00,
        'description' => 'The Infiniti QX80 is a full-size luxury SUV with powerful V8 performance, premium amenities, and three-row comfort.',
        'stock' => 2
    ],
    [
        'make' => 'Infiniti', 
        'model' => 'QX60', 
        'year' => 2023, 
        'color' => 'Moonbow Blue', 
        'vin' => 'JN8AZ2NE3M9123457', 
        'price' => 49895.00,
        'description' => 'The Infiniti QX60 is a three-row luxury crossover with elegant styling, spacious interior, and intelligent all-wheel drive.',
        'stock' => 3
    ],
    [
        'make' => 'Infiniti', 
        'model' => 'QX50', 
        'year' => 2023, 
        'color' => 'Hermosa Blue', 
        'vin' => 'JN8AZ2NE3M9123458', 
        'price' => 40300.00,
        'description' => 'The Infiniti QX50 is a luxury compact SUV featuring innovative engine technology, curated interior materials, and driver assistance features.',
        'stock' => 4
    ],
    [
        'make' => 'Infiniti', 
        'model' => 'Q50', 
        'year' => 2023, 
        'color' => 'Dynamic Sunstone Red', 
        'vin' => 'JN8AZ2NE3M9123459', 
        'price' => 42650.00,
        'description' => 'The Infiniti Q50 is a luxury sports sedan with twin-turbo performance, driver-centric cockpit, and distinctive styling.',
        'stock' => 3
    ]
];

// Create images directory if it doesn't exist
if (!file_exists('images')) {
    mkdir('images', 0777, true);
    echo "Created images directory<br>";
}

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO cars (make, model, year, color, vin, price, description, stock, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'available')");
$stmt->bind_param("ssidsssi", $make, $model, $year, $color, $vin, $price, $description, $stock);

// Insert each car
$count = 0;
foreach ($cars as $car) {
    $make = $car['make'];
    $model = $car['model'];
    $year = $car['year'];
    $color = $car['color'];
    $vin = $car['vin'];
    $price = $car['price'];
    $description = $car['description'];
    $stock = $car['stock'];
    
    if ($stmt->execute()) {
        $count++;
    } else {
        echo "Error adding car: " . $stmt->error . "<br>";
    }
}

// Don't close the connection if included in another file
if (!isset($cars_count)) {
    $stmt->close();
    $conn->close();
    
    echo "Successfully added $count sample cars to the database!";
    echo "<br><br><a href='index.php'>Return to Home Page</a>";
}
?> 