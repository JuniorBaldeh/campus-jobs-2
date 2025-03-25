<table>
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Recruiter ID</th>
            <th>Date</th>
            <th>Availability</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Total Hours</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Example of dynamic data - replace with your actual database query
        /*
        $visaStudents = getVisaStudents(); // Your database function
        foreach ($visaStudents as $student) {
            echo "<tr>
                <td>{$student['id']}</td>
                <td>{$student['recruiter_id']}</td>
                <td>{$student['date']}</td>
                <td>{$student['availability']}</td>
                <td>{$student['time_in']}</td>
                <td>{$student['time_out']}</td>
                <td>{$student['total_hours']}</td>
            </tr>";
        }
        */
        ?>
        <!-- Static example row -->
        <tr>
            <td>C2051</td>
            <td>02</td>
            <td>09-03-2025</td>
            <td>Yes</td>
            <td>9:00 AM</td>
            <td>05:00 PM</td>
            <td>8</td>
        </tr>
    </tbody>
</table>