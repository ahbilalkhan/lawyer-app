        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <div class="footer-section">
                        <h3><i class="fas fa-balance-scale"></i> LawyerConnect</h3>
                    </div>
                    <div class="footer-contact">
                        <span><i class="fas fa-phone"></i> +1 (555) 123-4567</span><br>
                        <span><i class="fas fa-envelope"></i> info@lawyerconnect.com</span><br>
                        <span><i class="fas fa-map-marker-alt"></i> 123 Legal Street, Law City</span>
                    </div>
                </div>
                <div class="footer-right">
                    <div class="footer-bottom">
                        <p>&copy; <?php echo date('Y'); ?> LawyerConnect. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php
    // Use same logic as header for consistent asset paths
    $isInPublicFolder = (strpos($_SERVER['SCRIPT_NAME'], '/public/') !== false);
    $basePath = $isInPublicFolder ? '' : 'public/';
    ?>
    <script src="<?php echo $basePath; ?>js/main.js"></script>
</body>
</html>
