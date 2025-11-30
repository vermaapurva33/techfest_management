<?php require 'includes/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>TechFest 2025</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    
    <div class="sidebar">
        <div class="logo">⚡ TechFest</div>
        <div class="nav-links">
            <a href="#" class="active">Home</a>
            <a href="#events">Events</a>
            <a href="#sponsors">Sponsors</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard_<?php echo $_SESSION['role']; ?>.php">My Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="auth.php">Login / Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-content">
        <div class="card" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); color: white; padding: 50px; text-align: center; margin-bottom: 40px;">
            <h1 style="font-size: 3rem; margin-bottom: 10px;">Innovate. Create. Conquer.</h1>
            <p style="font-size: 1.2rem; margin-bottom: 20px;">Join the ultimate technical showdown of the year.</p>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="auth.php" class="btn" style="background: white; color: var(--primary);">Get Started</a>
            <?php endif; ?>
        </div>

        <h2 id="events" style="margin-bottom: 20px;">Upcoming Events</h2>
        
        <div class="grid-container">
            <?php
            // Fetch events joined with Club names
            $sql = "SELECT e.*, c.club_name FROM events e LEFT JOIN clubs c ON e.club_id = c.club_id ORDER BY e.event_date ASC";
            $result = $conn->query($sql);

            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()):
            ?>
                <div class="card">
                    <?php $img = !empty($row['image_path']) ? 'assets/images/'.$row['image_path'] : 'https://via.placeholder.com/300'; ?>
                    <img src="<?php echo $img; ?>" class="card-img" alt="Event">
                    
                    <div class="card-body">
                        <span class="badge"><?php echo htmlspecialchars($row['club_name']); ?></span>
                        <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                        <p style="color: #666; font-size: 0.9rem; margin: 10px 0;">
                            <?php echo substr(htmlspecialchars($row['description']), 0, 80); ?>...
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                            <span style="font-weight: bold; color: var(--primary);">Entry: ₹<?php echo $row['entry_fee']; ?></span>
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'participant'): ?>
                                <a href="dashboard_participant.php" class="btn">Go to Dashboard</a>
                            <?php else: ?>
                                <a href="auth.php" style="color: var(--primary); font-size: 0.9rem;">Login to Join</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            } else {
                echo "<p>No events scheduled yet.</p>";
            }
            ?>
        </div>
        
        <h2 id="sponsors" style="margin: 40px 0 20px 0;">Our Sponsors</h2>
        <div class="card" style="padding: 20px;">
            <?php
            $sponsors = $conn->query("SELECT * FROM sponsors");
            while($s = $sponsors->fetch_assoc()) {
                echo "<span style='margin-right: 20px; font-weight: bold; font-size: 1.2rem; color: #555;'>".htmlspecialchars($s['name'])."</span>";
            }
            ?>
        </div>
    </div>
</body>
</html>