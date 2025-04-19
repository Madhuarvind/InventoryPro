document.addEventListener('DOMContentLoaded', function() {
    // Function to animate counting up to a target number
    function animateCounter(element, target) {
        const duration = 2000; // Animation duration in milliseconds
        const steps = 50; // Number of steps in the animation
        const stepDuration = duration / steps;
        let currentCount = 0;
        
        const increment = target / steps;
        
        const counter = setInterval(() => {
            currentCount += increment;
            if (currentCount >= target) {
                element.textContent = target.toLocaleString();
                clearInterval(counter);
            } else {
                element.textContent = Math.floor(currentCount).toLocaleString();
            }
        }, stepDuration);
    }

    // Function to fetch stats from the server
    function fetchStats() {
        fetch('fetch_stats.php')
            .then(response => response.json())
            .then(data => {
                // Get counter elements
                const productCount = document.getElementById('productCount');
                const orderCount = document.getElementById('orderCount');
                const userCount = document.getElementById('userCount');

                // Animate counters
                if (productCount) animateCounter(productCount, data.productCount || 0);
                if (orderCount) animateCounter(orderCount, data.orderCount || 0);
                if (userCount) animateCounter(userCount, data.userCount || 0);
            })
            .catch(error => {
                console.error('Error fetching stats:', error);
                // Set default values in case of error
                const counters = ['productCount', 'orderCount', 'userCount'];
                counters.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.textContent = '0';
                });
            });
    }

    // Initialize AOS animation library
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true
        });
    }

    // Fetch stats when page loads
    fetchStats();

    // Refresh stats every 5 minutes
    setInterval(fetchStats, 300000);
}));