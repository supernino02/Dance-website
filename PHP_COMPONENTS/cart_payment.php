<div class="col-lg-4">
    <div class="service-box">

        <h3 class="mb-5 pt-2 text-center fw-bold">Pagamento</h3>

        <form id="paymentForm" class="mb-5" autocomplete="off">
            <div class="form-outline mb-5">
                <input type="text" class="form-control form-control-lg" name="fake-card-number"
                    id="fakeCardNumber" />
                <label class="form-label" for="fakeCardNumber">Numero carta</label>
            </div>

            <div class="form-outline mb-5">
                <input type="text" id="fakeName" name="fake-name"
                    class="form-control form-control-lg" />
                <label class="form-label" for="fakeName">Nome sulla carta</label>
            </div>

            <div class="row">
                <div class="col-md-6 mb-5">
                    <div class="form-outline">
                        <input type="text" id="fakeExp" name="fake-exp"
                            class="form-control form-control-lg" />
                        <label class="form-label" for="fakeExp">Scadenza</label>
                    </div>
                </div>
                <div class="col-md-6 mb-5">
                    <div class="form-outline">
                        <input type="password" id="fakeCvv" name="fake-cvv"
                            class="form-control form-control-lg" />
                        <label class="form-label" for="fakeCvv">CVV</label>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button id="buyBtn" type="submit" class="btn btn-purple btn-purple-focus-purple btn-lg">Buy now</button>
            </div>
        </form>

    </div>

    <!-- Assistenza -->
    <div class="col-lg-12 mt-4" data-aos="fade-up" data-aos-delay="100">
        <div class="service-box d-flex flex-column justify-content-center align-items-center">
            <i class="bi bi-headset help-icon"></i>
            <h4>Hai domande?</h4>
            <a href="index.php#contatti" class="btn btn-purple btn-purple-focus-purple">
                <i class="bi bi-arrow-right"></i>
                Contattaci
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
    </div>
</div>