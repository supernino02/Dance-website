<footer id="footer" class="footer dark-background">

    <div class="footer-top">
        <div class="container">
            <div class="row gy-4">

                <div class="col-lg-4 footer-about">
                    <a href="index.php" class="logo d-flex align-items-center">
                        <span class="sitename">FBS Latin Empire</span>
                    </a>
                </div>

                <div class="col-lg-4 footer-links">
                    <h4>Link Utili</h4>
                    <ul>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="terms_of_service.php"> Terms of service</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="privacy_policy.php"> Privacy policy</a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-4 footer-links">
                    <h4>Riferimenti</h4>
                    <ul>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="index.php">Home</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="courses.php">Corsi</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="events.php">Eventi</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="index.php#collaboratori">Collaboratori</a>
                        </li>
                        <li>
                            <i class="bi bi-chevron-right"></i>
                            <a href="index.php#contatti">Contatti</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>

<div id="toasters" class="position-fixed bottom-0 end-0 p-3">
    <div id="toastContainer"></div>
</div>

<?php ComponentManager::includeComponent("biscotto") ?>