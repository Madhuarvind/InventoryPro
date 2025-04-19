<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";
$id = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $supplier = trim($_POST['supplier']);
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';

    // Handling image upload
    $image = $_FILES['image'];
    $imagePath = ''; // Default image path

    if ($image['name']) {
        // Set the image file path and name
        $imageName = time() . '_' . $image['name'];
        $imageTmpName = $image['tmp_name'];
        $imageSize = $image['size'];
        $imageError = $image['error'];
        $imageType = $image['type'];

        // Define allowed image file types
        $allowedTypes = array('image/jpeg', 'image/png', 'image/jpg');

        // Check if the uploaded file is an allowed image type
        if (in_array($imageType, $allowedTypes)) {
            if ($imageError === 0) {
                if ($imageSize < 5000000) { // Max size of 5MB
                    $uploadDir = 'uploads/products/'; // Directory where images will be stored
                    $imagePath = $uploadDir . $imageName;

                    // Move the uploaded image to the server directory
                    if (move_uploaded_file($imageTmpName, $imagePath)) {
                        // Image uploaded successfully
                    } else {
                        $error = "❌ Error uploading image.";
                    }
                } else {
                    $error = "❌ Image file size exceeds the limit of 5MB.";
                }
            } else {
                $error = "❌ Error in image upload.";
            }
        } else {
            $error = "❌ Invalid image type. Only JPG, JPEG, and PNG are allowed.";
        }
    }

    if ($category) {
        // Generate a purely numeric or alphanumeric unique barcode (12 characters)
        $id = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);
        $barcode = $id; // Use product ID as barcode

        // Insert into Database with barcode and image path
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, supplier, category, barcode, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdissss", $name, $price, $quantity, $supplier, $category, $barcode, $imagePath);

        if ($stmt->execute()) {
            $success = "✅ Product added successfully!";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "❌ Please select a category!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <style>
        :root {
            --primary-color: #6366f1;
            --hover-color: #4f46e5;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: rgba(241, 245, 249, 0.5);
        }

        .upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
        }

        .preview-image {
            max-width: 200px;
            border-radius: 12px;
            margin: 1rem auto;
            display: none;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--hover-color);
            transform: translateY(-2px);
        }

        .barcode-container {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container py-5" style="max-width: 600px;">
        <div class="glass-card">
            <h2 class="text-center mb-4">➕ Add New Product</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="productForm">
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-tag me-2"></i>Product Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-list me-2"></i>Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option value="ELECTRONICS">📱 Electronics</option>
                        <option value="FURNITURE">🛋️ Furniture</option>
                        <option value="CLOTHING">👕 Clothing</option>
                        <option value="GROCERY">🛒 Grocery</option>
                    </select>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-rupee-sign me-2"></i>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-cubes me-2"></i>Quantity</label>
                        <input type="number" name="quantity" class="form-control" placeholder="0" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-truck me-2"></i>Supplier</label>
                    <input type="text" name="supplier" class="form-control" placeholder="Enter supplier name" required>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-image me-2"></i>Product Image</label>
                    <div class="upload-area" id="uploadArea">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                            <p class="mt-2 mb-0">Drag & drop or click to upload</p>
                            <small class="text-muted">(Max size: 5MB, Formats: JPG, PNG)</small>
                        </div>
                        <input type="file" name="image" class="d-none" id="fileInput" accept="image/*">
                        <img src="#" class="preview-image" id="imagePreview" alt="Image preview">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>Add Product
                    </button>
                    <a href="products.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
            </form>

            <?php if (!empty($id)): ?>
            <div class="barcode-container text-center mt-4">
                <h5 class="mb-3">Generated Barcode</h5>
                <svg id="barcode" class="mb-3"></svg>
                <button class="btn btn-success" onclick="downloadBarcode()">
                    <i class="fas fa-download me-2"></i>Download Barcode
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Image Upload Interactions
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('imagePreview');

        uploadArea.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Drag & Drop Handling
        ['dragenter', 'dragover'].forEach(event => {
            uploadArea.addEventListener(event, () => {
                uploadArea.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(event => {
            uploadArea.addEventListener(event, () => {
                uploadArea.classList.remove('dragover');
            });
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        // Barcode Generation
        function generateBarcode(id) {
            JsBarcode("#barcode", id, {
                format: "CODE128",
                displayValue: true,
                lineColor: "#1e293b",
                width: 2,
                height: 50,
                fontOptions: "bold"
            });
        }

        <?php if (!empty($id)): ?>
            generateBarcode("<?php echo $id; ?>");
        <?php endif; ?>

        function downloadBarcode() {
            const svg = document.querySelector("#barcode");
            const svgData = new XMLSerializer().serializeToString(svg);
            const blob = new Blob([svgData], { type: "image/svg+xml" });
            const url = URL.createObjectURL(blob);
            
            const a = document.createElement("a");
            a.href = url;
            a.download = "barcode_<?php echo $id; ?>.svg";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>