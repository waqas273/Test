<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST requests for adding or removing a user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        // Add a user
        if ($_POST['action'] == 'add') {
            $name = $conn->real_escape_string($_POST['name']);
            $email = $conn->real_escape_string($_POST['email']);
            $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
            if ($conn->query($sql) === TRUE) {
                $message = "User added successfully.";
            } else {
                $message = "Error: " . $conn->error;
            }
        }
        // Remove a user
        if ($_POST['action'] == 'delete') {
            $id = intval($_POST['id']);
            $sql = "DELETE FROM users WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                $message = "User removed successfully.";
            } else {
                $message = "Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Responsive User Management</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Responsive User Management</h1>
  </header>
  <main>
    <section>
      <h2>Add User</h2>
      <?php if(isset($message)) { echo "<p class='message'>$message</p>"; } ?>
      <form action="index.php" method="post" class="user-form">
        <input type="hidden" name="action" value="add">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <button type="submit">Add User</button>
      </form>
    </section>

    <section>
      <h2>User List</h2>
      <?php
      // Retrieve all users
      $sql = "SELECT id, name, email FROM users";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
          echo "<table>";
          echo "<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr></thead>";
          echo "<tbody>";
          while($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>" . $row["id"] . "</td>
                      <td>" . $row["name"] . "</td>
                      <td>" . $row["email"] . "</td>
                      <td>
                        <form action='index.php' method='post' onsubmit='return confirm(\"Are you sure you want to remove this user?\");'>
                          <input type='hidden' name='action' value='delete'>
                          <input type='hidden' name='id' value='" . $row["id"] . "'>
                          <button type='submit' class='remove-btn'>Remove</button>
                        </form>
                      </td>
                    </tr>";
          }
          echo "</tbody>";
          echo "</table>";
      } else {
          echo "<p>No users found.</p>";
      }
      ?>
    </section>
  </main>
  <footer>
    <p>&copy; 2025 My Website</p>
  </footer>
</body>
</html>
<?php $conn->close(); ?>
