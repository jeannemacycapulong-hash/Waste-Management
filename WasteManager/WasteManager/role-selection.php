<?php
// role-selection.php - Role Selection after login
require_once 'config.php';
requireLogin();
include 'header.php';
?>

<div class="role-selection">
    <h2>Select User Role</h2>
    <p class="welcome-user">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>! 
        Choose your interface.
    </p>
    
    <div class="role-cards">
        <!-- Villager Card -->
        <div class="role-card villager">
            <i class="fas fa-home role-icon"></i>
            <h3>Villager</h3>
            <ul class="role-options">
                <li><i class="fas fa-calendar-alt"></i> View Garbage Collection Schedule</li>
                <li><i class="fas fa-route"></i> View daily collection routes</li>
                <li><i class="fas fa-clipboard-check"></i> Monitor Village Cleanliness</li>
                <li><i class="fas fa-check-circle"></i> Confirmation Of Garbage Collect</li>
            </ul>
            <a href="villager-dashboard.php" class="role-link">
                GO TO DASHBOARD →
            </a>
        </div>

        <!-- Collector Card -->
        <div class="role-card collector">
            <i class="fas fa-truck role-icon"></i>
            <h3>Collector</h3>
            <ul class="role-options">
                <li><i class="fas fa-dumpster"></i> Collect garbage from villages</li>
                <li><i class="fas fa-sync-alt"></i> Update collection status</li>
                <li><i class="fas fa-clipboard-check"></i> Track pickups</li>
                <li><i class="fas fa-exclamation-triangle"></i> Report issues</li>
            </ul>
            <a href="collector-dashboard.php" class="role-link">
                COLLECTOR DASHBOARD →
            </a>
        </div>

        <!-- Admin Card -->
        <div class="role-card admin">
            <i class="fas fa-chart-pie role-icon"></i>
            <h3>Admin</h3>
            <ul class="role-options">
                <li><i class="fas fa-dashboard"></i> View Dashboard and Reports</li>
                <li><i class="fas fa-flag"></i> Manage Issues</li>
                <li><i class="fas fa-users"></i> Monitor Users</li>
                <li><i class="fas fa-bell"></i> Send Notifications</li>
            </ul>
            <a href="admin-dashboard.php" class="role-link">
                ADMIN DASHBOARD →
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<style>
/* Role Selection Page */
.role-selection {
    background: white;
    border-radius: 30px;
    padding: 3rem;
    box-shadow: 0 30px 50px rgba(0,0,0,0.1);
}

.role-selection h2 {
    text-align: center;
    color: #2e7d32;
    margin-bottom: 0.5rem;
    font-size: 2.5rem;
}

.welcome-user {
    text-align: center;
    margin-bottom: 2.5rem;
    color: #666;
    font-size: 1.2rem;
}

.role-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 1rem;
}

.role-card {
    background: #f9f9f9;
    border-radius: 20px;
    padding: 2rem 1.8rem;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 2px solid transparent;
}

.role-card.villager:hover { border-color: #8bc34a; }
.role-card.collector:hover { border-color: #ff9800; }
.role-card.admin:hover { border-color: #9c27b0; }

.role-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.role-icon {
    font-size: 3.5rem;
    margin-bottom: 1.2rem;
    color: #8bc34a;
}

.role-card h3 {
    font-size: 2rem;
    margin-bottom: 1.2rem;
    color: #2e7d32;
}

.role-options {
    list-style: none;
    margin: 1rem 0 1.8rem;
    flex-grow: 1;
}

.role-options li {
    padding: 0.8rem 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #555;
    border-bottom: 1px dashed #ddd;
    font-size: 1rem;
}

.role-options li.click-here {
    color: #8bc34a;
    font-weight: 600;
}

.role-link {
    width: 100%;
    padding: 1rem;
    background: #8bc34a;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.role-link:hover {
    background: #2e7d32;
    transform: scale(1.02);
}

/* Responsive Design */
@media (max-width: 992px) {
    .role-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .role-cards {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .role-selection {
        padding: 1.5rem;
    }

    .role-card h3 {
        font-size: 1.6rem;
    }
}
</style>