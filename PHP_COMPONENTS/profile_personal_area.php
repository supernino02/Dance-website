<section id="showprofile" class="showprofile section">
    <div class="container">
        <div class="row gy-3 d-flex justify-content-center">
            <div class="col-lg-12" data-aos="fade-up" data-aos-delay="100">
                <div class="service-box">

                    <h4>
                        <a id="showProfileToggler" class="text-decoration-none d-flex align-items-center"
                            data-bs-toggle="collapse" href="#personalAreaContentCollapse" role="button"
                            aria-expanded="false" aria-controls="personalAreaContentCollapse">
                            Informazioni Personali
                            <i class="bi bi-chevron-down ms-2" id="personalAreaDropdownIcon"></i>
                        </a>
                    </h4>

                    <div class="showprofile-box-content">

                        <div id="showprofileLoadingSpinner" class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>

                        <div id="personalAreaContentCollapse" class="collapse">
                            <div id="profileContent" class="row d-flex">
                                <div class="col-md-6 mb-2">
                                    <span class="label">Nome:</span>
                                    <div class="showprofile-box-item">
                                        <span class="user_name"></span>
                                        <button class="btn-edit" id="editName"><i class="fas fa-pen"></i></button>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <span class="label">Cognome:</span>
                                    <div class="showprofile-box-item">
                                        <span class="user_surname"></span>
                                        <button class="btn-edit" id="editSurname"><i class="fas fa-pen"></i></button>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <span class="label">Email:</span>
                                    <div class="showprofile-box-item">
                                        <span class="user_email"></span>
                                        <button type="button" id="editEmail" class="btn-edit-gray" data-toggle="tooltip"
                                            data-placement="right"
                                            title="Al momento questa funzionalità è disabilitata, scrivi a un Admin!">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <span class="label">Password:</span>
                                    <div class="showprofile-box-item">
                                        <span class="user_password">********</span>
                                        <button class="btn-edit" id="editPassword"><i class="fas fa-pen"></i></button>
                                    </div>
                                </div>


                                <div class="col-md-6 mb-2">
                                    <span class="label">Telefono (opzionale):</span>
                                    <div class="showprofile-box-item">
                                        <span class="user_phone_number"></span>
                                        <button class="btn-edit" id="editPhoneNumber"><i
                                                class="fas fa-pen"></i></button>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <span class="label">Codice Fiscale (opzionale):</span>
                                    <div class="showprofile-box-item">
                                        <span class="user_fiscal_code"></span>
                                        <button class="btn-edit" id="editFiscalCode"><i class="fas fa-pen"></i></button>
                                    </div>
                                </div>


                                <div class="col-md-12 mb-2">
                                    <span class="label">Genere:</span>
                                    <div class="showprofile-box-item">
                                        <span class="user_gender"></span>
                                        <button class="btn-edit" id="editGender"><i class="fas fa-pen"></i></button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div id="showprofileResult" class="d-flex justify-content-center mt-3"></div>

                </div><!-- End Show Profile Box -->
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label for="newInput" class="form-label" id="editModalFieldLabel"></label>
                            <input type="text" class="form-control" id="newInput" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</a>
                    <a class="btn btn-purple btn-purple-focus-purple" id="saveBtn">Salva</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Modifica Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="passwordForm">
                        <div class="mb-3">
                            <label for="oldPassword" class="form-label">Vecchia Password</label>
                            <input type="password" class="form-control" id="oldPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nuova Password</label>
                            <input type="password" class="form-control" id="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Conferma Password</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</a>
                    <a class="btn btn-purple" id="savePasswordBtn">Salva</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gender Modal -->
    <div class="modal fade" id="editGenderModal" tabindex="-1" aria-labelledby="editGenderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGenderModalLabel">Modifica Genere</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editGenderForm">
                        <div class="mb-3">
                            <label for="genderSelectInput" class="form-label">Genere</label>
                            <select class="form-control" id="genderSelectInput" required>
                                <option value="" disabled selected>Select your gender</option>
                                <option value="agender">Agender</option>
                                <option value="abimegender">Abimegender</option>
                                <option value="adamas">Adamas gender</option>
                                <option value="aerogender">Aerogender</option>
                                <option value="aesthetigender">Aesthetigender</option>
                                <option value="affectugender">Affectugender</option>
                                <option value="agenderflux">Agenderflux</option>
                                <option value="alexigender">Alexigender</option>
                                <option value="aliusgender">Aliusgender</option>
                                <option value="amaregender">Amaregender</option>
                                <option value="ambigender">Ambigender</option>
                                <option value="ambonec">Ambonec</option>
                                <option value="amicagender">Amicagender</option>
                                <option value="androgyne">Androgyne</option>
                                <option value="anesigender">Anesigender</option>
                                <option value="angenital">Angenital</option>
                                <option value="anogender">Anogender</option>
                                <option value="anongender">Anongender</option>
                                <option value="antegender">Antegender</option>
                                <option value="anxiegender">Anxiegender</option>
                                <option value="apagender">Apagender</option>
                                <option value="apconsugender">Apconsugender</option>
                                <option value="astergender">Astergender</option>
                                <option value="astralgender">Astral gender</option>
                                <option value="autigender">Autigender</option>
                                <option value="autogender">Autogender</option>
                                <option value="axigender">Axigender</option>
                                <option value="bigender">Bigender</option>
                                <option value="biogender">Biogender</option>
                                <option value="blurgender">Blurgender</option>
                                <option value="boyflux">Boyflux</option>
                                <option value="burstgender">Burstgender</option>
                                <option value="caelgender">Caelgender</option>
                                <option value="cassgender">Cassgender</option>
                                <option value="cassflux">Cassflux</option>
                                <option value="cavusgender">Cavusgender</option>
                                <option value="cendgender">Cendgender</option>
                                <option value="ceterogender">Ceterogender</option>
                                <option value="ceterofluid">Ceterofluid</option>
                                <option value="cisgender">Cisgender</option>
                                <option value="cloudgender">Cloudgender</option>
                                <option value="collgender">Collgender</option>
                                <option value="colorgender">Colorgender</option>
                                <option value="commogender">Commogender</option>
                                <option value="condigender">Condigender</option>
                                <option value="deliciagender">Deliciagender</option>
                                <option value="demifluid">Demifluid</option>
                                <option value="demiflux">Demiflux</option>
                                <option value="demigender">Demigender</option>
                                <option value="domgender">Domgender</option>
                                <option value="duragender">Duragender</option>
                                <option value="egogender">Egogender</option>
                                <option value="epicene">Epicene</option>
                                <option value="esspigender">Esspigender</option>
                                <option value="exgender">Exgender</option>
                                <option value="existigender">Existigender</option>
                                <option value="femfluid">Femfluid</option>
                                <option value="femgender">Femgender</option>
                                <option value="fluidflux">Fluidflux</option>
                                <option value="gemigender">Gemigender</option>
                                <option value="genderblank">Genderblank</option>
                                <option value="genderflow">Genderflow</option>
                                <option value="genderfluid">Genderfluid</option>
                                <option value="genderfuzz">Genderfuzz</option>
                                <option value="genderflux">Genderflux</option>
                                <option value="genderpuck">Genderpuck</option>
                                <option value="genderqueer">Genderqueer</option>
                                <option value="genderwitched">Genderwitched</option>
                                <option value="girlflux">Girlflux</option>
                                <option value="healgender">Healgender</option>
                                <option value="mirrorgender">Mirrorgender</option>
                                <option value="omnigender">Omnigender</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    <button type="button" class="btn btn-purple" id="saveGenderBtn">Salva modifiche</button>
                </div>
            </div>
        </div>
    </div>

</section>