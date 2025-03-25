<script>
    function showTab(tabName) {
        // Hide all tab content
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show the selected tab content
        document.getElementById(tabName).classList.add('active');

        // Update active tab button
        document.querySelectorAll('.tabs button').forEach(button => {
            button.classList.remove('active');
        });

        event.currentTarget.classList.add('active');
    }
</script>
</body>

</html>