<!-- Products Section -->
<section id="products" class="products section">
    <div class="container">
        <div class="row gy-3 d-flex justify-content-center">
            <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                <div class="service-box">

                    <h4>
                        <a class="text-decoration-none d-flex align-items-center" data-bs-toggle="collapse"
                            href="#productsContentCollapse" role="button" aria-expanded="false"
                            aria-controls="productsContentCollapse">
                            Prodotti Acquistati
                            <i class="bi bi-chevron-down ms-2" id="productsDropdownIcon"></i>
                        </a>
                    </h4>

                    <div id="productsLoadingSpinner" class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>

                    <div id="productsContentCollapse" class="collapse">
                        <div id="productsContent">

                            <!-- i prodotti vengono caricati tramite ajax -->

                        </div>
                    </div>

                </div><!-- End Products Box -->
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal fade" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Header del Modal -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="reviewModalLabel">Lascia una Recensione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Corpo del Modal -->
                <div class="modal-body">
                    <form>
                        <!-- Input per la valutazione -->
                        <div class="mb-4">
                            <label class="form-label">Valutazione</label>
                            <div class="d-flex align-items-center">
                                <!-- Stelle per la valutazione -->
                                <div id="starRating" class="me-3">
                                    <i class="bi bi-star" data-value="1"></i>
                                    <i class="bi bi-star" data-value="2"></i>
                                    <i class="bi bi-star" data-value="3"></i>
                                    <i class="bi bi-star" data-value="4"></i>
                                    <i class="bi bi-star" data-value="5"></i>
                                </div>
                                <!-- Selezione numerica -->
                                <input type="number" id="starValue" class="form-control w-auto" value="0" min="0"
                                    max="5" step="0.5" readonly>
                            </div>
                        </div>

                        <!-- Input per il commento -->
                        <div class="mb-4">
                            <label for="reviewText" class="form-label">Commento</label>
                            <textarea class="form-control" id="reviewText" rows="4" maxlength="256"
                                placeholder="Scrivi la tua recensione"></textarea>
                        </div>
                    </form>
                </div>

                <!-- Footer del Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <a id="sendReview" href="#" type="button" class="btn btn-purple btn-purple-focus-purple">Invia Recensione</a>
                </div>
            </div>
        </div>
    </div>

</section><!-- End Products Section -->

<!-- Orders Section -->
<section id="orders" class="orders section">
    <div class="container">
        <div class="row gy-5 d-flex justify-content-center">
            <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                <div class="service-box">
                    <!-- Collapse Trigger with Dropdown Icon -->
                    <h4>
                        <a class="text-decoration-none d-flex align-items-center" data-bs-toggle="collapse"
                            href="#ordersContentCollapse" role="button" aria-expanded="false"
                            aria-controls="ordersContentCollapse">
                            Ordini
                            <i class="bi bi-chevron-down ms-2" id="ordersDropdownIcon"></i>
                        </a>
                    </h4>

                    <!-- Spinner (optional, if you need a loading state) -->
                    <div id="ordersLoadingSpinner" class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>

                    <!-- Collapsible Content -->
                    <div id="ordersContentCollapse" class="collapse">
                        <div id="ordersContent">
                            <!-- gli ordini vengono caricati tramite ajax -->
                        </div>
                    </div>

                </div><!-- End Orders Box -->
            </div>
        </div>
    </div>
</section>