          <div class="row gy-3">
              <!-- Carosello -->
              <div class="col-lg-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
                  <div class="service-box w-100 h-100">
                      <!--placeholder-->
                      <div id="loadingSpinnerCarousel" class="d-flex justify-content-center align-items-center w-100">
                          <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Loading...</span>
                          </div>
                      </div>

                      <div id="imagesCarousel" class="d-none carousel slide h-100" data-bs-ride="carousel">
                          <div class="carousel-indicators">
                              <!-- indicatori caricati con ajax -->
                          </div>
                          <div class="carousel-inner h-100">
                              <!-- immagini caricate con ajax -->
                          </div>
                          <button class="carousel-control-prev" type="button" data-bs-target="#imagesCarousel"
                              data-bs-slide="prev">
                              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                              <span class="visually-hidden">Previous</span>
                          </button>
                          <button class="carousel-control-next" type="button" data-bs-target="#imagesCarousel"
                              data-bs-slide="next">
                              <span class="carousel-control-next-icon" aria-hidden="true"></span>
                              <span class="visually-hidden">Next</span>
                          </button>
                      </div>
                  </div>
              </div>

              <!-- Informazioni e tasti -->
              <div class="col-lg-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
                  <div id="productDetails" class="service-box w-100 h-100 d-flex flex-column">
                      <h4 class="placeholder col-8 position-relative">Dettagli</h4>
                      <!-- dettagli prodotto caricati con ajax -->
                      <div id="productDescription" class="placeholder-glow product-description mb-0 bottom-line">
                          <!-- Placeholder per la descrizione del prodotto -->
                          <div class="placeholder-glow mb-0 bottom-line">
                              <p class="placeholder col-12"></p>
                              <p class="placeholder col-10"></p>
                              <p class="placeholder col-8"></p>
                          </div>
                          <br>
                          <!-- Placeholder per i dettagli -->
                          <dl class="row bottom-line">
                              <dt class="col-6 placeholder-glow"><span class="placeholder col-6"></span></dt>
                              <dd class="col-6 placeholder-glow"><span class="placeholder col-8"></span></dd>
                              <dt class="col-6 placeholder-glow"><span class="placeholder col-7"></span></dt>
                              <dd class="col-6 placeholder-glow"><span class="placeholder col-3"></span></dd>
                              <dt class="col-6 placeholder-glow"><span class="placeholder col-9"></span></dt>
                              <dd class="col-6 placeholder-glow"><span class="placeholder col-2"></span></dd>
                              <dt class="col-6 placeholder-glow"><span class="placeholder col-3"></span></dt>
                              <dd class="col-6 placeholder-glow"><span class="placeholder col-2"></span></dd>
                          </dl>
                          <!-- Placeholder per i pulsanti -->
                          <div class="d-flex justify-content-between mt-auto">
                              <div class="btn btn-purple placeholder col-4"></div>
                              <div class="btn btn-purple placeholder col-4"></div>
                          </div>
                      </div>
                      <br>
                  </div>
              </div>
          </div>



          <!-- Fullscreen Image Overlay -->
          <div id="fullscreenImage" class="position-fixed align-middle top-0 start-0 d-flex justify-content-center align-items-center d-none">
              <img id="fullscreenImg" src="MULTIMEDIA/imgs/no-icon.png" alt="fullscreen image" class="img-fluid w-100 h-100">
          </div>
