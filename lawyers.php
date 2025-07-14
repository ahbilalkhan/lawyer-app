<?php
$page_title = "Find Lawyers";
include 'header.php';
require_once 'db.php';

// Get search parameters
$location = $_GET['location'] ?? '';
$service = $_GET['service'] ?? '';
$experience = $_GET['experience'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Build search query
$whereConditions = ["u.is_active = 1", "lp.is_verified = 1"];
$params = [];

if (!empty($location)) {
    $whereConditions[] = "lp.location LIKE ?";
    $params[] = "%$location%";
}

if (!empty($service)) {
    $whereConditions[] = "EXISTS (SELECT 1 FROM lawyer_services ls WHERE ls.lawyer_id = lp.id AND ls.service_type = ?)";
    $params[] = $service;
}

if (!empty($experience)) {
    switch ($experience) {
        case '1-3':
            $whereConditions[] = "lp.experience_years BETWEEN 1 AND 3";
            break;
        case '4-7':
            $whereConditions[] = "lp.experience_years BETWEEN 4 AND 7";
            break;
        case '8-15':
            $whereConditions[] = "lp.experience_years BETWEEN 8 AND 15";
            break;
        case '15+':
            $whereConditions[] = "lp.experience_years >= 15";
            break;
    }
}

$whereClause = implode(' AND ', $whereConditions);

try {
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total 
                 FROM lawyer_profiles lp 
                 JOIN users u ON lp.user_id = u.id 
                 WHERE $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalLawyers = $countStmt->fetch()['total'];
    $totalPages = ceil($totalLawyers / $limit);

    // Get lawyers
    $sql = "SELECT 
                lp.id,
                u.full_name,
                lp.specialization,
                lp.experience_years,
                lp.location,
                lp.consultation_fee,
                lp.rating,
                lp.total_reviews,
                lp.bio,
                lp.office_address,
                lp.availability_status,
                GROUP_CONCAT(DISTINCT ls.service_type) as services
            FROM lawyer_profiles lp
            JOIN users u ON lp.user_id = u.id
            LEFT JOIN lawyer_services ls ON lp.id = ls.lawyer_id
            WHERE $whereClause
            GROUP BY lp.id
            ORDER BY lp.rating DESC, lp.total_reviews DESC
            LIMIT $limit OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $lawyers = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $lawyers = [];
}
?>

<div class="lawyers-container">
    <div class="search-header">
        <h1><i class="fas fa-search"></i> Find Legal Professionals</h1>
        <p>Search and connect with qualified lawyers in your area</p>
    </div>

    <!-- Search Filters -->
    <div class="search-filters">
        <form method="GET" class="filters-form">
            <div class="filter-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Enter city or state">
            </div>
            
            <div class="filter-group">
                <label for="service">Legal Service</label>
                <select id="service" name="service">
                    <option value="">All Services</option>
                    <option value="criminal" <?php echo $service === 'criminal' ? 'selected' : ''; ?>>Criminal Law</option>
                    <option value="family" <?php echo $service === 'family' ? 'selected' : ''; ?>>Family Law</option>
                    <option value="divorce" <?php echo $service === 'divorce' ? 'selected' : ''; ?>>Divorce</option>
                    <option value="civil" <?php echo $service === 'civil' ? 'selected' : ''; ?>>Civil Law</option>
                    <option value="corporate" <?php echo $service === 'corporate' ? 'selected' : ''; ?>>Corporate Law</option>
                    <option value="property" <?php echo $service === 'property' ? 'selected' : ''; ?>>Property Law</option>
                    <option value="immigration" <?php echo $service === 'immigration' ? 'selected' : ''; ?>>Immigration</option>
                    <option value="tax" <?php echo $service === 'tax' ? 'selected' : ''; ?>>Tax Law</option>
                    <option value="labor" <?php echo $service === 'labor' ? 'selected' : ''; ?>>Labor Law</option>
                    <option value="intellectual_property" <?php echo $service === 'intellectual_property' ? 'selected' : ''; ?>>Intellectual Property</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="experience">Experience</label>
                <select id="experience" name="experience">
                    <option value="">Any Experience</option>
                    <option value="1-3" <?php echo $experience === '1-3' ? 'selected' : ''; ?>>1-3 years</option>
                    <option value="4-7" <?php echo $experience === '4-7' ? 'selected' : ''; ?>>4-7 years</option>
                    <option value="8-15" <?php echo $experience === '8-15' ? 'selected' : ''; ?>>8-15 years</option>
                    <option value="15+" <?php echo $experience === '15+' ? 'selected' : ''; ?>>15+ years</option>
                </select>
            </div>
            
            <div class="filter-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>

    <!-- Results Header -->
    <div class="results-header">
        <h2>
            <?php if ($totalLawyers > 0): ?>
                Found <?php echo $totalLawyers; ?> lawyer<?php echo $totalLawyers > 1 ? 's' : ''; ?>
                <?php if ($location || $service || $experience): ?>
                    matching your criteria
                <?php endif; ?>
            <?php else: ?>
                No lawyers found
            <?php endif; ?>
        </h2>
        
        <?php if ($location || $service || $experience): ?>
            <div class="active-filters">
                <span>Active filters:</span>
                <?php if ($location): ?>
                    <span class="filter-tag">Location: <?php echo htmlspecialchars($location); ?></span>
                <?php endif; ?>
                <?php if ($service): ?>
                    <span class="filter-tag">Service: <?php echo ucfirst(str_replace('_', ' ', $service)); ?></span>
                <?php endif; ?>
                <?php if ($experience): ?>
                    <span class="filter-tag">Experience: <?php echo $experience; ?> years</span>
                <?php endif; ?>
                <a href="lawyers.php" class="clear-filters">Clear all</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Lawyers Grid -->
    <?php if (!empty($lawyers)): ?>
        <div class="lawyers-grid">
            <?php foreach ($lawyers as $lawyer): ?>
                <div class="lawyer-card">
                    <div class="lawyer-avatar">
                        <?php echo strtoupper(substr($lawyer['full_name'], 0, 1)); ?>
                    </div>
                    
                    <div class="lawyer-info">
                        <h3><?php echo htmlspecialchars($lawyer['full_name']); ?></h3>
                        <p class="specialization"><?php echo htmlspecialchars($lawyer['specialization']); ?></p>
                        <p class="experience"><?php echo $lawyer['experience_years']; ?> years experience</p>
                        <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($lawyer['location']); ?></p>
                        
                        <div class="rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo $i <= $lawyer['rating'] ? 'filled' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-text"><?php echo $lawyer['rating']; ?> (<?php echo $lawyer['total_reviews']; ?> reviews)</span>
                        </div>
                        
                        <div class="services">
                            <?php if ($lawyer['services']): ?>
                                <?php foreach (explode(',', $lawyer['services']) as $service): ?>
                                    <span class="service-tag"><?php echo ucfirst(str_replace('_', ' ', trim($service))); ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="consultation-fee">
                            <strong>Consultation: $<?php echo number_format($lawyer['consultation_fee'], 2); ?></strong>
                        </div>
                        
                        <div class="availability">
                            <span class="status-badge <?php echo $lawyer['availability_status']; ?>">
                                <?php echo ucfirst($lawyer['availability_status']); ?>
                            </span>
                        </div>
                        
                        <div class="lawyer-actions">
                            <a href="profile.php?id=<?php echo $lawyer['id']; ?>" class="btn btn-secondary">
                                <i class="fas fa-user"></i> View Profile
                            </a>
                            <a href="book-appointment.php?lawyer_id=<?php echo $lawyer['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Book Appointment
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>
                
                <span class="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="btn btn-secondary">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h3>No lawyers found</h3>
            <p>Try adjusting your search criteria or <a href="lawyers.php">browse all lawyers</a>.</p>
        </div>
    <?php endif; ?>
</div>

<style>
.lawyers-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 0;
}

.search-header {
    text-align: center;
    margin-bottom: 3rem;
}

.search-header h1 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.search-filters {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
}

.filters-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #2c3e50;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.filter-group input:focus,
.filter-group select:focus {
    outline: none;
    border-color: #3498db;
}

.results-header {
    margin-bottom: 2rem;
}

.results-header h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.active-filters {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-tag {
    background: #3498db;
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
}

.clear-filters {
    color: #e74c3c;
    text-decoration: none;
    font-weight: 500;
}

.clear-filters:hover {
    text-decoration: underline;
}

.lawyers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.lawyer-card {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.lawyer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.lawyer-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #3498db;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    font-weight: bold;
    margin: 0 auto 1rem;
}

.lawyer-info {
    text-align: center;
}

.lawyer-info h3 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.specialization {
    color: #3498db;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.experience {
    color: #666;
    margin-bottom: 0.5rem;
}

.location {
    color: #666;
    margin-bottom: 1rem;
}

.rating {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stars {
    display: flex;
    gap: 0.1rem;
}

.star {
    color: #ddd;
    font-size: 1.2rem;
}

.star.filled {
    color: #f39c12;
}

.rating-text {
    font-size: 0.9rem;
    color: #666;
}

.services {
    margin-bottom: 1rem;
}

.service-tag {
    display: inline-block;
    background: #ecf0f1;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}

.consultation-fee {
    margin-bottom: 1rem;
    color: #27ae60;
}

.availability {
    margin-bottom: 1.5rem;
}

.status-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-badge.available {
    background: #d4edda;
    color: #155724;
}

.status-badge.busy {
    background: #fff3cd;
    color: #856404;
}

.status-badge.unavailable {
    background: #f8d7da;
    color: #721c24;
}

.lawyer-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.lawyer-actions .btn {
    flex: 1;
    text-align: center;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    margin-top: 3rem;
}

.page-info {
    color: #666;
    font-weight: 500;
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
    color: #666;
}

.no-results i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.no-results h3 {
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .filters-form {
        grid-template-columns: 1fr;
    }
    
    .lawyers-grid {
        grid-template-columns: 1fr;
    }
    
    .lawyer-actions {
        flex-direction: column;
    }
    
    .active-filters {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .pagination {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<?php include 'footer.php'; ?>
