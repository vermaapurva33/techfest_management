<?php
require 'includes/db.php';

echo "<h2>🌱 Seeding Database...</h2>";

// 1. CLEAR EXISTING DATA (To avoid duplicates)
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['users', 'clubs', 'events', 'accommodation', 'registrations', 'bookings', 'sponsors', 'results'];
foreach($tables as $t) {
    $conn->query("TRUNCATE TABLE $t");
}
$conn->query("SET FOREIGN_KEY_CHECKS = 1");
echo "✅ Old data cleared.<br>";

// 2. INSERT USERS (Password for ALL users will be '12345')
$pass = password_hash("12345", PASSWORD_DEFAULT);

$users = [
    // [Name, Email, Role, Phone]
    ["Admin User", "admin@test.com", "admin", "9999999999"],
    ["John Organizer", "org@test.com", "organizer", "9876543210"],
    ["Sarah Judge", "judge@test.com", "judge", "8888888888"],
    ["Alice Participant", "alice@test.com", "participant", "7777777777"],
    ["Bob Participant", "bob@test.com", "participant", "6666666666"],
    ["Charlie Participant", "charlie@test.com", "participant", "5555555555"]
];

foreach ($users as $u) {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $u[0], $u[1], $pass, $u[2], $u[3]);
    $stmt->execute();
}
echo "✅ Users created (Password: 12345).<br>";

// 3. INSERT CLUBS
$clubs = ["Coding Club", "Robotics Society", "Literary Club", "Music & Arts", "Gaming Guild"];
foreach ($clubs as $c) {
    // Assign 'John Organizer' (ID 2) as coordinator
    $conn->query("INSERT INTO clubs (club_name, coordinator_id) VALUES ('$c', 2)");
}
echo "✅ Clubs created.<br>";

// 4. INSERT ACCOMMODATION
$rooms = [
    ["Single Room", 1, 500.00, "Block A"],
    ["Double Shared", 2, 300.00, "Block B"],
    ["Dormitory", 10, 150.00, "Block C"]
];
foreach ($rooms as $r) {
    $stmt = $conn->prepare("INSERT INTO accommodation (room_type, capacity, cost_per_night, location) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sids", $r[0], $r[1], $r[2], $r[3]);
    $stmt->execute();
}
echo "✅ Accommodation added.<br>";

// 5. INSERT SPONSORS
$sponsors = [
    ["TechCorp", 50000.00],
    ["InnovateX", 25000.00],
    ["RedBull", 100000.00]
];
foreach ($sponsors as $s) {
    $conn->query("INSERT INTO sponsors (name, amount_contributed) VALUES ('{$s[0]}', {$s[1]})");
}
echo "✅ Sponsors added.<br>";

// 6. INSERT EVENTS
// We insert events linked to Club ID 1 and Organizer ID 2
$events = [
    ["Hackathon 2025", "24-hour coding battle.", "2025-11-10 09:00:00", "Auditorium", 200.00, "upcoming"],
    ["RoboWar", "Battle of bots.", "2025-11-12 14:00:00", "Ground", 500.00, "upcoming"],
    ["Poetry Slam", "Express your thoughts.", "2025-10-05 10:00:00", "Seminar Hall", 50.00, "completed"],
    ["AI Workshop", "Learn basics of ML.", "2025-11-15 11:00:00", "Lab 1", 100.00, "upcoming"]
];

foreach ($events as $e) {
    $stmt = $conn->prepare("INSERT INTO events (event_name, description, event_date, venue, entry_fee, status, club_id, organizer_id) VALUES (?, ?, ?, ?, ?, ?, 1, 2)");
    // Note: We are not setting image_path here, so it will fallback to placeholder in your code
    $stmt->bind_param("ssssds", $e[0], $e[1], $e[2], $e[3], $e[4], $e[5]);
    $stmt->execute();
}
echo "✅ Events created.<br>";

// 7. INSERT REGISTRATIONS (Alice & Bob registering for Hackathon & RoboWar)
// User IDs: Alice=4, Bob=5. Event IDs: Hackathon=1, RoboWar=2
$regs = [
    [4, 1, 'completed'], // Alice -> Hackathon
    [4, 2, 'pending'],   // Alice -> RoboWar
    [5, 1, 'completed']  // Bob -> Hackathon
];
foreach ($regs as $rg) {
    $conn->query("INSERT INTO registrations (user_id, event_id, payment_status) VALUES ({$rg[0]}, {$rg[1]}, '{$rg[2]}')");
}
echo "✅ Registrations added.<br>";

// 8. INSERT RESULTS (For the completed 'Poetry Slam' - Event ID 3)
// Winner: Charlie (ID 6)
$conn->query("INSERT INTO results (event_id, winner_user_id, rank, comments) VALUES (3, 6, 1, 'Excellent performance!')");
echo "✅ Results added.<br>";

echo "<h3 style='color:green'>🎉 Database Successfully Populated!</h3>";
echo "<a href='index.php'>Go to Home</a>";
?>