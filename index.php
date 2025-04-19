<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional Inventory Management System">
    <title>InventoryPro | Modern Inventory Management</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="logo.png" type="image/png">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --dark: #1b263b;
            --light: #f8f9fa;
            --gradient-start: #4361ee;
            --gradient-end: #7209b7;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Navbar Styling */
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--dark);
        }

        .navbar-nav .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            color: var(--primary) !important;
        }

        .btn-get-started {
            background: linear-gradient(to right, var(--gradient-start), var(--gradient-end));
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-get-started:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            padding: 100px 0 50px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(114, 9, 183, 0.1));
            z-index: -1;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(114, 9, 183, 0.1), rgba(67, 97, 238, 0.1));
            z-index: -1;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background-color: white;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 50px;
            text-align: center;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
        }

        .feature-description {
            color: #6c757d;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
        }

        .stat-card {
            text-align: center;
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            text-align: center;
            background-color: white;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
        }

        .cta-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        .footer {
            padding: 50px 0;
            background-color: var(--dark);
            color: white;
        }

        .footer-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: white;
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-section {
                padding: 80px 0 40px;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .hero-section {
                padding: 60px 0 30px;
            }
            .hero-image {
                margin-top: 40px;
            }
            .feature-card {
                margin-bottom: 20px;
            }
        }

        /* Animation */
        [data-aos] {
            visibility: visible !important;
        }
        body {
            opacity: 1 !important;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-top me-2">
                <span>InventoryPro</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="#hero">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#stats">Stats</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-get-started" href="login.php">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div data-aos="fade-right">
                        <h1 class="hero-title">Inventory Management</h1>
                        <p class="hero-subtitle">Streamline your inventory process with our powerful management system. Track products, manage stock levels, and generate reports all in one place.</p>
                        <div class="d-flex gap-3">
                            <a href="login.php" class="btn btn-get-started">Login</a>
                            <a href="register.php" class="btn btn-outline-primary px-4 py-2 rounded-pill">Register</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div data-aos="fade-left" class="text-center">
                        <!-- SVG Illustration -->
                        <svg width="100%" height="400" viewBox="0 0 800 600">
                            <polygon points="400,500 200,400 400,300 600,400" fill="#e9ecef" stroke="#dee2e6"/>
                            <polygon points="400,300 400,100 600,200 600,400" fill="#4361ee" fill-opacity="0.8"/>
                            <polygon points="400,300 200,400 200,200 400,100" fill="#4895ef" fill-opacity="0.9"/>
                            <polygon points="450,250 450,180 500,205 500,275" fill="#f8f9fa"/>
                            <polygon points="250,300 250,250 300,225 300,275" fill="#f8f9fa"/>
                            <polygon points="325,225 325,175 375,150 375,200" fill="#f8f9fa"/>
                            <polygon points="450,450 450,400 500,425 500,475" fill="#fd7e14"/>
                            <polygon points="525,425 525,375 575,400 575,450" fill="#20c997"/>
                            <polygon points="250,450 250,400 300,375 300,425" fill="#7209b7"/>
                            <circle cx="350" cy="350" r="15" fill="#212529"/>
                            <line x1="350" y1="365" x2="350" y2="400" stroke="#212529"/>
                            <line x1="350" y1="375" x2="330" y2="390" stroke="#212529"/>
                            <line x1="350" y1="375" x2="370" y2="390" stroke="#212529"/>
                            <line x1="350" y1="400" x2="335" y2="425" stroke="#212529"/>
                            <line x1="350" y1="400" x2="365" y2="425" stroke="#212529"/>
                            <rect x="380" y="375" width="20" height="25" fill="#f8f9fa"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Key Features</h2>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-box"></i></div>
                        <h3 class="feature-title">Product Management</h3>
                        <p class="feature-description">Easily add, edit, and organize products with detailed information.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h3 class="feature-title">Real-time Analytics</h3>
                        <p class="feature-description">Get insights with real-time data visualization and reports.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-truck"></i></div>
                        <h3 class="feature-title">Order Management</h3>
                        <p class="feature-description">Track orders and manage shipments efficiently.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="stat-card">
                        <div class="stat-number" id="productCount">0</div>
                        <div class="stat-label">Total Products</div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-number" id="orderCount">0</div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-number" id="userCount">0</div>
                        <div class="stat-label">Active Users</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title" data-aos="fade-up">Ready to Get Started?</h2>
            <p class="cta-subtitle" data-aos="fade-up">Join thousands of businesses using our system.</p>
            <div data-aos="fade-up">
                <div class="row g-3 justify-content-center">
                    <!-- CTA buttons remain same -->
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3 class="footer-title">InventoryPro</h3>
                    <p>Streamline your inventory management with our powerful and intuitive system.</p>
                    <div class="social-icons mt-3">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h3 class="footer-title">Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h3 class="footer-title">Features</h3>
                    <ul class="footer-links">
                        <li><a href="#">Product Management</a></li>
                        <li><a href="#">Inventory Tracking</a></li>
                        <li><a href="#">Order Management</a></li>
                        <li><a href="#">Reports & Analytics</a></li>
                        <li><a href="#">User Management</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h3 class="footer-title">Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Inventory Street, Suite 456</li>
                        <li><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
                        <li><i class="fas fa-envelope me-2"></i> info@inventorypro.com</li>
                    </ul>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0">© 2023 InventoryPro. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Smooth scrolling for navigation links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all links
                    navLinks.forEach(navLink => navLink.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    const targetId = this.getAttribute('href');
                    const targetSection = document.querySelector(targetId);
                    
                    if (targetSection) {
                        window.scrollTo({
                            top: targetSection.offsetTop - 80, // Offset for navbar height
                            behavior: 'smooth'
                        });
                    }
                });
            });
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init();
            
            // Animate numbers counting up
            function animateValue(id, start, end, duration) {
                let obj = document.getElementById(id);
                let range = end - start;
                let startTime = null;
                
                function updateCounter(timestamp) {
                    if (!startTime) startTime = timestamp;
                    let progress = timestamp - startTime;
                    let increment = Math.floor(progress / duration * range);
                    obj.innerHTML = start + increment;
                    if (progress < duration) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        obj.innerHTML = end;
                    }
                }
                
                requestAnimationFrame(updateCounter);
            }
            
            // Stats are now handled by the external stats.js file
            // which fetches data from fetch_stats.php
            // No need for inline stats fetching code
        });
        
        // Fade-in animation for elements
            const fadeElements = document.querySelectorAll('.fade-in');
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            });
            
            fadeElements.forEach(element => {
                observer.observe(element);
            });
        });
    </script>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Animated counters
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.textContent = Math.floor(progress * (end - start) + start);
                if (progress < 1) window.requestAnimationFrame(step);
            };
            window.requestAnimationFrame(step);
        }

        // Fetch stats (mock data)
        const stats = {
            products: 1274,
            orders: 3428,
            users: 562
        };

        document.addEventListener('DOMContentLoaded', () => {
            animateValue(document.getElementById('productCount'), 0, stats.products, 2000);
            animateValue(document.getElementById('orderCount'), 0, stats.orders, 2000);
            animateValue(document.getElementById('userCount'), 0, stats.users, 2000);
            
            // Smooth scroll
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });
        });
    </script>
</body>
</html>