<?php 
require 'includes/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'participant') {
    header("Location: auth.php"); exit();
}

// HANDLE EVENT REGISTRATION
if (isset($_POST['register_event'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if already registered (Dual check: DB constraint handles it, but good for UX)
    $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id, payment_status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $user_id, $event_id);
    
    if($stmt->execute()) {
        echo "<script>alert('Registered Successfully!');</script>";
    } else {
        echo "<script>alert('You are already registered for this event.');</script>";
    }
}

// HANDLE ACCOMMODATION BOOKING
if (isset($_POST['book_room'])) {
    $room_id = $_POST['room_id'];
    $cost = $_POST['cost'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, total_cost) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("iid", $user_id, $room_id, $cost);
    if($stmt->execute()) {
        echo "<script>alert('Room Booked Successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">⚡ Dashboard</div>
        <div class="nav-links">
            <a href="#events">Upcoming Events</a>
            <a href="#my_registrations">My Registrations</a>
            <a href="#accommodation">Accommodation</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

        <h3 id="events" style="margin-top: 30px; margin-bottom: 15px;">Register for Events</h3>
        <div class="grid-container">
            <?php
            $sql = "SELECT * FROM events WHERE status = 'upcoming'";
            $res = $conn->query($sql);
            while($row = $res->fetch_assoc()):
                $img = !empty($row['image_path']) ? 'assets/images/'.$row['image_path'] : 'https://via.placeholder.com/300';
            ?>
            <div class="card">
                <img src="<?php echo $img; ?>" class="card-img">
                <div class="card-body">
                    <h4><?php echo htmlspecialchars($row['event_name']); ?></h4>
                    <p style="font-size: 0.9rem; color: #666; margin: 10px 0;">
                        <?php echo substr($row['description'], 0, 50); ?>...
                    </p>
                    <p style="font-weight: bold;">Fee: ₹<?php echo $row['entry_fee']; ?></p>
                    
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="event_id" value="<?php echo $row['event_id']; ?>">
                        <button type="submit" name="register_event" class="btn" style="width: 100%;">Register Now</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <h3 id="accommodation" style="margin-top: 50px;">Book Accommodation</h3>
        <table style="width: 100%;">
            <thead><tr><th>Room Type</th><th>Capacity</th><th>Location</th><th>Cost/Night</th><th>Action</th></tr></thead>
            <tbody>
                <?php
                $rooms = $conn->query("SELECT * FROM accommodation");
                while($r = $rooms->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $r['room_type']; ?></td>
                    <td><?php echo $r['capacity']; ?> Persons</td>
                    <td><?php echo $r['location']; ?></td>
                    <td>₹<?php echo $r['cost_per_night']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="room_id" value="<?php echo $r['room_id']; ?>">
                            <input type="hidden" name="cost" value="<?php echo $r['cost_per_night']; ?>">
                            <button type="submit" name="book_room" class="btn" style="padding: 5px 15px;">Book</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3 id="my_registrations" style="margin-top: 50px;">My Registrations</h3>
        <table style="width: 100%;">
            <thead><tr><th>Event Name</th><th>Date</th><th>Payment Status</th></tr></thead>
            <tbody>
                <?php
                $uid = $_SESSION['user_id'];
                $myRegs = $conn->query("SELECT e.event_name, e.event_date, r.payment_status FROM registrations r JOIN events e ON r.event_id = e.event_id WHERE r.user_id = $uid");
                
                if($myRegs->num_rows > 0) {
                    while($m = $myRegs->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($m['event_name']); ?></td>
                    <td><?php echo $m['event_date']; ?></td>
                    <td><span class="badge"><?php echo $m['payment_status']; ?></span></td>
                </tr>
                <?php 
                    endwhile;
                } else {
                    echo "<tr><td colspan='3'>You haven't registered for any events yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>