document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    // Get form input values
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();

    // Validation
    if (!username || !email || !password) {
        alert('Please fill in all fields.');
        return; // Stop further execution if validation fails
    }

    // Email validation using a simple regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return;
    }

    // Password validation (example: at least 8 characters)
    if (password.length < 8) {
        alert('Password must be at least 8 characters long.');
        return;
    }

    // Simulate a login request (replace with actual API call)
    simulateLogin(username, email, password)
        .then(response => {
            alert('Login successful!');
            // Redirect to another page or perform additional actions
            window.location.href = 'dashboard.html'; // Example redirect
        })
        .catch(error => {
            alert('Login failed: ' + error.message);
        });
});

// Simulate a login request (replace with actual API call)
function simulateLogin(username, email, password) {
    return new Promise((resolve, reject) => {
        // Simulate a delay for network request
        setTimeout(() => {
            // Example: Check if credentials are valid
            const validUsername = 'testuser';
            const validEmail = 'test@example.com';
            const validPassword = 'password123';

            if (username === validUsername && email === validEmail && password === validPassword) {
                resolve({ message: 'Login successful' });
            } else {
                reject(new Error('Invalid username, email, or password.'));
            }
        }, 1000); // Simulate 1 second delay
    });
}

// navbar.js

// Function to handle navigation
function navigateTo(page) {
    window.location.href = page;
}

// Add event listeners to navbar links
document.addEventListener('DOMContentLoaded', function () {
    // Employee Link
    const employeeLink = document.getElementById('employeeLink');
    if (employeeLink) {
        employeeLink.addEventListener('click', function () {
            navigateTo('employee.php');
        });
    }

    // Core Payroll Link
    const corePayrollLink = document.getElementById('corePayrollLink');
    if (corePayrollLink) {
        corePayrollLink.addEventListener('click', function () {
            navigateTo('core_payroll.php');
        });
    }

    // Timesheets Link
    const timesheetsLink = document.getElementById('timesheetsLink');
    if (timesheetsLink) {
        timesheetsLink.addEventListener('click', function () {
            navigateTo('timesheets.php');
        });
    }

    // Logout Link
    const logoutLink = document.getElementById('logoutLink');
    if (logoutLink) {
        logoutLink.addEventListener('click', function () {
            navigateTo('logout.php');
        });
    }
});