<!-- Registration Form for Students -->
<form id="register-student">
    <h2>Register as Student</h2>
    <label for="student-name">Name:</label>
    <input type="text" id="student-name" name="student-name" required>
    <label for="student-nim">NIM:</label>
    <input type="text" id="student-nim" name="student-nim" pattern="\d{10}" required>
    <label for="student-email">Email:</label>
    <input type="email" id="student-email" name="student-email" required>
    <label for="student-department">Department:</label>
    <select id="student-department" name="student-department" required>
        <!-- Options for departments -->
    </select>
    <label for="student-program">Study Program:</label>
    <select id="student-program" name="student-program" required>
        <!-- Options for study programs -->
    </select>
    <label for="student-password">Password:</label>
    <input type="password" id="student-password" name="student-password" required>
    <button type="submit">Register</button>
</form>

<!-- Registration Form for Admins -->
<form id="register-admin">
    <h2>Register as Admin</h2>
    <label for="admin-name">Name:</label>
    <input type="text" id="admin-name" name="admin-name" required>
    <label for="admin-id">Admin ID:</label>
    <input type="text" id="admin-id" name="admin-id" pattern="\d{10}" required>
    <label for="admin-email">Email:</label>
    <input type="email" id="admin-email" name="admin-email" required>
    <label for="admin-password">Password:</label>
    <input type="password" id="admin-password" name="admin-password" required>
    <button type="submit">Register</button>
</form>

<!-- Registration Form for CDC -->
<form id="register-cdc">
    <h2>Register as CDC</h2>
    <label for="cdc-name">Name:</label>
    <input type="text" id="cdc-name" name="cdc-name" required>
    <label for="cdc-id">CDC ID:</label>
    <input type="text" id="cdc-id" name="cdc-id" pattern="\d{10}" required>
    <label for="cdc-email">Email:</label>
    <input type="email" id="cdc-email" name="cdc-email" required>
    <label for="cdc-password">Password:</label>
    <input type="password" id="cdc-password" name="cdc-password" required>
    <button type="submit">Register</button>
</form>

<!-- Registration Form for Companies -->
<form id="register-company">
    <h2>Register as Company</h2>
    <label for="company-name">Company Name:</label>
    <input type="text" id="company-name" name="company-name" required>
    <label for="company-reg-number">Registration Number:</label>
    <input type="text" id="company-reg-number" name="company-reg-number" required>
    <label for="company-email">Email:</label>
    <input type="email" id="company-email" name="company-email" required>
    <label for="company-address">Address:</label>
    <input type="text" id="company-address" name="company-address" required>
    <label for="company-contact">Contact Person:</label>
    <input type="text" id="company-contact" name="company-contact" required>
    <label for="company-phone">Phone Number:</label>
    <input type="text" id="company-phone" name="company-phone" required>
    <label for="company-password">Password:</label>
    <input type="password" id="company-password" name="company-password" required>
    <button type="submit">Register</button>
</form>
