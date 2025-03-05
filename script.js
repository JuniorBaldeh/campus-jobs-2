document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // Basic validation
    if (username && email && password) {
        alert('Login successful!');
        // Here you can add code to handle the login process
    } else {
        alert('Please fill in all fields.');
    }
});