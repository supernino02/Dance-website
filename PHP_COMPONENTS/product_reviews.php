<div class="col-12 w-100 d-flex row gy-3" data-aos="fade-up" data-aos-delay="200">
    <div class="col-12 w-100">
        <div id="productReviews" class="service-box position-relative col-12 w-100 flex-column">
            <div class="mb-4" id="reviewsSummary">
                <div class="d-flex w-100">
                    <h3 class="flex-grow-1 mb-3 mt-0">Valutazioni di questo prodotto</h3>
                    <div class="ml-auto mt-0" id="btnReviewDiv">
                        <!--eventualmente ci va il bottone delle reviews-->
                    </div>
                </div>
                <!--Inserisco un div placeholder, verrà poi sostituito -->
                <div class="row align-items-center">
                    <!-- Punteggio Medio -->
                    <div class="col-12 col-sm-4 order-2 order-sm-1 text-center pt-3" id="ratingAverageContainer">
                        <h3 class="display-4 fw-bold" id="ratingAverage">0</h3>
                        <div id="starsAverage">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p class="mb-0 text-muted" id="reviewsCount">0 recensioni</p>
                    </div>
                    <!-- Barre di Progresso -->
                    <div class="col-12 col-sm-8 order-1 order-sm-2 pt-3" id="progressBarsMajorContainer">
                        <!-- Barre di progresso generate dinamicamente -->
                        <div id="progressBarsContainer" class="w-100">
                            <!-- Progress Bar 1 -->
                            <div class="row align-items-center">
                                <div class="col-1 ps-0">
                                    <span class="emoji">😞</span> <!-- Emoji for very low happiness -->
                                </div>
                                <div class="col-8 col-sm-9 col-md-9 col-xl-10">
                                    <div class="progress w-95">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-3 col-sm-2 col-md-2 col-xl-1 text-end pe-0">
                                    <span class=" perc_reviews d-flex justify-content-end align-items-center"><span
                                            class="text-end">0</span>%</span>
                                </div>
                            </div>

                            <!-- Progress Bar 2 -->
                            <div class=" row align-items-center">
                                <div class="col-1 ps-0">
                                    <span class="emoji">😕</span> <!-- Emoji for low happiness -->
                                </div>
                                <div class="col-8 col-sm-9 col-md-9 col-xl-10">
                                    <div class="progress w-95">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-3 col-sm-2 col-md-2 col-xl-1 text-end pe-0">
                                    <span class="perc_reviews d-flex justify-content-end align-items-center"><span
                                            class="text-end">0</span>%</span>
                                </div>

                            </div>

                            <!-- Progress Bar 3 -->
                            <div class="row align-items-center">
                                <div class="col-1 ps-0">
                                    <span class="emoji">😐</span> <!-- Emoji for neutral happiness -->
                                </div>
                                <div class="col-8 col-sm-9 col-md-9 col-xl-10">
                                    <div class="progress w-95">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-3 col-sm-2 col-md-2 col-xl-1 text-end pe-0">
                                    <span class="perc_reviews d-flex justify-content-end align-items-center"><span
                                            class="text-end">0</span>%</span>
                                </div>
                            </div>

                            <!-- Progress Bar 4 -->
                            <div class="row align-items-center">
                                <div class="col-1 ps-0">
                                    <span class="emoji">😊</span> <!-- Emoji for medium-high happiness -->
                                </div>
                                <div class="col-8 col-sm-9 col-md-9 col-xl-10">
                                    <div class="progress w-95">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-3 col-sm-2 col-md-2 col-xl-1 text-end pe-0">
                                    <span class="perc_reviews d-flex justify-content-end align-items-center"><span
                                            class="text-end">0</span>%</span>
                                </div>
                            </div>

                            <!-- Progress Bar 5 -->
                            <div class="row align-items-center">
                                <div class="col-1 ps-0">
                                    <span class="emoji">😃</span> <!-- Emoji for high happiness -->
                                </div>
                                <div class="col-8 col-sm-9 col-md-9 col-xl-10">
                                    <div class="progress w-95">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-3 col-sm-2 col-md-2 col-xl-1 text-end pe-0">
                                    <span class="perc_reviews d-flex justify-content-end align-items-center"><span
                                            class="text-end">0</span>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fine placeholder -->

            </div>
            <!-- Bottone per aprire/chiudere il div recensioni e ordinarle -->
            <div id="toggleReviews" class="row d-flex">
                <div class="col d-flex pe-0" data-bs-toggle="collapse" tabindex="0" data-bs-target="#reviewsContainer"
                    role="button" aria-expanded="false" aria-controls="reviewsContainer">
                    Recensioni
                    <i class="bi bi-chevron-down ms-2 me-0 float-end"></i>
                </div>
                <div id="orderByDiv" class="col dropdown text-end pe-0 ps-0">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="orderBybtn"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Ordina per
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderBybtn">
                        <a id="btnOrderDate" class="dropdown-item active" href="#">Data</a>
                        <a id="btnOrderEval" class="dropdown-item" href="#">Valutazione</a>
                    </div>
                </div>
            </div>

            <!-- Spinner di caricamento -->
            <div id="loadingSpinnerReviews" class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>

            <!-- Div delle recensioni che si apre/chiude -->
            <div id="reviewsContainer" class="collapse">
                <div id="reviews" class="col-12 w-100 flex-column">
                    <!-- Recensioni prodotto caricate con ajax -->
                </div>
            </div>

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
                            <input type="number" id="starValue" class="form-control w-auto" value="0" min="0" max="5"
                                step="0.5" readonly>
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
                <button id="sendReview" type="button" class="btn  btn-purple btn-purple-focus-purple">Invia
                    Recensione</button>
            </div>
        </div>
    </div>
</div>