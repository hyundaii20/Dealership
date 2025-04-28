<?php
// Include required files
require_once 'Database.php';

// Initialize database connection
$db = new Database();
$conn = Database::getConnection();

// Process search filters
$where_clauses = [];
$params = [];
$types = '';

// Make filter - exact match instead of LIKE
$selected_make = '';
if (isset($_GET['make']) && !empty($_GET['make'])) {
    $selected_make = $_GET['make'];
    $where_clauses[] = "make = ?";
    $params[] = $_GET['make'];
    $types .= 's';
}

// Model filter - exact match instead of LIKE
if (isset($_GET['model']) && !empty($_GET['model'])) {
    $where_clauses[] = "model = ?";
    $params[] = $_GET['model'];
    $types .= 's';
}

// Year filter
if (isset($_GET['year']) && !empty($_GET['year'])) {
    $where_clauses[] = "year = ?";
    $params[] = (int)$_GET['year'];
    $types .= 'i';
}

// Price range filter
if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
    $where_clauses[] = "price >= ?";
    $params[] = (float)$_GET['min_price'];
    $types .= 'd';
}

if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
    $where_clauses[] = "price <= ?";
    $params[] = (float)$_GET['max_price'];
    $types .= 'd';
}

// Build the query
$sql = "SELECT * FROM cars";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY make, model";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);

// Get unique makes for filters
$makes_result = $conn->query("SELECT DISTINCT make FROM cars ORDER BY make");
$makes = $makes_result->fetch_all(MYSQLI_ASSOC);

// Get models based on selected make or all models if no make is selected
if (!empty($selected_make)) {
    $models_query = "SELECT DISTINCT model FROM cars WHERE make LIKE ? ORDER BY model";
    $stmt = $conn->prepare($models_query);
    $like_make = "%" . $selected_make . "%";
    $stmt->bind_param("s", $like_make);
    $stmt->execute();
    $models_result = $stmt->get_result();
} else {
    $models_result = $conn->query("SELECT DISTINCT model FROM cars ORDER BY model");
}
$models = $models_result->fetch_all(MYSQLI_ASSOC);

// Get unique years for filters
$years_result = $conn->query("SELECT DISTINCT year FROM cars ORDER BY year DESC");
$years = $years_result->fetch_all(MYSQLI_ASSOC);

// JavaScript for dynamic filtering
$js = <<<EOT
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get references to the make and model selectors
    const makeSelect = document.getElementById('make');
    const modelSelect = document.getElementById('model');
    
    // Store all models from the database
    const allModels = [];
    
    // Populate the allModels array
    const modelOptions = Array.from(modelSelect.options);
    modelOptions.forEach(option => {
        if (option.value) {
            allModels.push({
                make: option.getAttribute('data-make'),
                model: option.value,
                text: option.text
            });
        }
    });
    
    // Save current selected model value to restore after filtering
    const currentModelValue = modelSelect.value;
    
    // Function to update model options based on selected make
    function updateModelOptions() {
        const selectedMake = makeSelect.value;
        
        // Clear current model options except the first one (All Models)
        while (modelSelect.options.length > 1) {
            modelSelect.remove(1);
        }
        
        // If no make is selected, show all models
        if (!selectedMake) {
            allModels.forEach(modelData => {
                const option = new Option(modelData.text, modelData.model);
                option.setAttribute('data-make', modelData.make);
                modelSelect.add(option);
            });
        } else {
            // Filter models for the selected make
            const filteredModels = allModels.filter(modelData => 
                modelData.make === selectedMake
            );
            
            // Add filtered models to the select
            filteredModels.forEach(modelData => {
                const option = new Option(modelData.text, modelData.model);
                option.setAttribute('data-make', modelData.make);
                modelSelect.add(option);
            });
        }
        
        // Try to restore the previously selected model if it exists in the new options
        if (currentModelValue) {
            for (let i = 0; i < modelSelect.options.length; i++) {
                if (modelSelect.options[i].value === currentModelValue) {
                    modelSelect.selectedIndex = i;
                    break;
                }
            }
        }
    }
    
    // Perform initial update of model options if make is already selected
    updateModelOptions();
    
    // Listen for changes on the make select
    makeSelect.addEventListener('change', updateModelOptions);
});
</script>
EOT;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Cars - Car Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/cars.css">
</head>
<body>
    <div class="container">
        <h1>Browse Our Cars</h1>
        
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="cars.php">Browse Cars</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="cart.php">Shopping Cart</a></li>
            </ul>
        </nav>
        
        <div class="filter-section">
            <h2>Search and Filter</h2>
            <form class="filter-form" method="GET" action="cars.php">
                <div class="form-group">
                    <label for="make">Make</label>
                    <select name="make" id="make">
                        <option value="">All Makes</option>
                        <?php foreach ($makes as $make): ?>
                            <option value="<?= htmlspecialchars($make['make']) ?>" <?= (isset($_GET['make']) && $_GET['make'] == $make['make']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($make['make']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="model">Model</label>
                    <select name="model" id="model">
                        <option value="">All Models</option>
                        <?php 
                        // Get all makes to use for data attributes
                        $make_model_query = "SELECT make, model FROM cars GROUP BY make, model ORDER BY make, model";
                        $make_model_result = $conn->query($make_model_query);
                        $make_model_pairs = $make_model_result->fetch_all(MYSQLI_ASSOC);
                        
                        // Create mapping of models to makes
                        $model_to_make = [];
                        foreach ($make_model_pairs as $pair) {
                            $model_to_make[$pair['model']] = $pair['make'];
                        }
                        
                        foreach ($models as $model): 
                            $make_for_model = isset($model_to_make[$model['model']]) ? $model_to_make[$model['model']] : '';
                        ?>
                            <option 
                                value="<?= htmlspecialchars($model['model']) ?>" 
                                data-make="<?= htmlspecialchars($make_for_model) ?>"
                                <?= (isset($_GET['model']) && $_GET['model'] == $model['model']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($model['model']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="year">Year</label>
                    <select name="year" id="year">
                        <option value="">All Years</option>
                        <?php foreach ($years as $year): ?>
                            <option value="<?= htmlspecialchars($year['year']) ?>" <?= (isset($_GET['year']) && $_GET['year'] == $year['year']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year['year']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="min_price">Min Price</label>
                    <input type="number" name="min_price" id="min_price" placeholder="Min Price" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="max_price">Max Price</label>
                    <input type="number" name="max_price" id="max_price" placeholder="Max Price" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : '' ?>">
                </div>
                
                <button type="submit" class="search-btn">Search Cars</button>
            </form>
        </div>
        
        <?php if (count($cars) > 0): ?>
            <div class="car-grid">
                <?php foreach ($cars as $car): ?>
                    <div class="car-card">
                        <div class="car-info">
                            <div class="car-title"><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></div>
                            <div class="car-price">€<?= number_format($car['price'], 2) ?></div>
                            <div class="car-details">
                                Year: <?= htmlspecialchars($car['year']) ?><br>
                                Color: <?= htmlspecialchars($car['color']) ?><br>
                                Stock: <?= htmlspecialchars($car['stock']) ?>
                            </div>
                            <div class="car-status <?= $car['status'] == 'available' ? 'status-available' : 'status-sold-out' ?>">
                                <?= ucfirst($car['status']) ?>
                            </div>
                            <p><?= htmlspecialchars(substr($car['description'], 0, 100)) ?>...</p>
                            <a href="car_details.php?id=<?= $car['car_id'] ?>" class="btn">View Details</a>
                            <?php if ($car['status'] == 'available' && $car['stock'] > 0): ?>
                                <a href="add_to_cart.php?id=<?= $car['car_id'] ?>" class="btn" style="background-color: #28a745;">Add to Cart</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <h3>No cars found matching your criteria</h3>
                <p>Try adjusting your search filters or browse all cars.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php echo $js; ?>
</body>
</html> 