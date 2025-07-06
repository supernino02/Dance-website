<!-- Modal -->
<div class="modal fade" id="cookieModal" tabindex="-1" role="dialog" aria-labelledby="cookieModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div id="cookie-modal" class="modal-content">
            <button type="button" class="btn btn-close col-1 top-1 ml-1" id='close-modal'
                data-bs-dismiss="modal"></button>
            <div class="modal-header col-12">
                <nav class="w-100">
                    <div class="w-100 nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link col-4 text-center active" id="nav-intro-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-intro" type="button" role="tab" aria-controls="nav-intro"
                            aria-selected="true">
                            Consenso
                        </button>
                        <button class="nav-link col-4 text-center " id="nav-dettagli-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-dettagli" type="button" role="tab" aria-controls="nav-dettagli"
                            aria-selected="false">
                            Dettagli
                        </button>
                        <button class="nav-link col-4 text-center " id="nav-cookies-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-cookies" type="button" role="tab" aria-controls="nav-cookies"
                            aria-selected="false">
                            Gestisci cookie
                        </button>
                    </div>
                </nav>
            </div>
            <div class="modal-body col-12 h-100 overflow-auto">
                <div class="tab-content" id="cookieModalTabContent">
                    <div class="tab-pane show" id="nav-intro" role="tabpanel" aria-labelledby="nav-intro-tab">
                        <h5 id="cookieModalLabel">Questo sito utilizza Cookie🍪</h5>
                        <p>
                            Utilizziamo i cookie per migliorare la tua esperienza di navigazione.<br>
                            Alcuni cookie sono necessari per il corretto funzionamento del sito, mentre altri ci aiutano
                            a comprendere come viene utilizzato, in modo da poterlo migliorare.<br>
                            Non utilizziamo cookie di terze parti o per finalità di marketing.
                        </p>
                        <p>Se desideri maggiori informazioni, puoi consultare la nostra <a href="privacy_policy.php"
                                target="_blank">Politica sui Cookie</a>.</p>
                    </div>
                    <div class="tab-pane col-12" id="nav-dettagli" role="tabpanel" aria-labelledby="nav-dettagli-tab">
                        <p>
                            Utilizziamo diversi tipi di Cookie, ed è importante che gli utenti ne conoscano il
                            funzionamento.
                        </p>

                        <div id="cookie-dettagli-collapsable-container">
                            <!-- Essential Cookies -->
                            <div class="row col-12 collapse-cookie-info d-flex align-items-center">
                                <div class="mb-2 col-auto" data-bs-toggle="collapse" data-bs-target="#cookie-essential"
                                    role="button" aria-expanded="false" aria-controls="cookie-essential">
                                    <span>Essential</span>
                                    <i class="bi bi-chevron-down float-end ms-3"></i>
                                </div>
                                <div class="form-check form-switch ms-auto col-auto">
                                    <input id="check-cookie-essential" class="form-check-input btn-purple"
                                        type="checkbox" role="switch" checked disabled>
                                </div>
                            </div>
                            <div class="collapse" id="cookie-essential"
                                data-bs-parent="#cookie-dettagli-collapsable-container">
                                <div class="card card-body overflow-auto">
                                    <p>
                                        I cookie essenziali sono necessari per il corretto funzionamento del nostro
                                        sito.<br>
                                        Questi cookie permettono di navigare e utilizzare funzionalità fondamentali,
                                        come mantenere attive le tue preferenze durante la sessione di navigazione.<br>
                                        <span class="fw-bold">Non possono essere disattivati</span>, poiché senza di
                                        essi il sito non funzionerebbe correttamente.
                                    </p>
                                </div>
                            </div>

                            <!-- Analytics Cookies -->
                            <div class="row col-12 collapse-cookie-info d-flex align-items-center">
                                <div class="mb-2 col-auto d-flex align-items-center" data-bs-toggle="collapse"
                                    data-bs-target="#cookie-analytics" role="button" aria-expanded="false"
                                    aria-controls="cookie-analytics">
                                    <span class="pr-3">Analytics</span>
                                    <i class="bi bi-chevron-down ms-3"></i>
                                </div>
                                <div class="form-check form-switch ms-auto col-auto">
                                    <input id="check-cookie-analytics"
                                        class="form-check-input check-cookie-type btn-purple" type="checkbox"
                                        role="switch" data-cookie-type='analytic' checked>
                                </div>
                            </div>
                            <div class="collapse" id="cookie-analytics"
                                data-bs-parent="#cookie-dettagli-collapsable-container">
                                <div class="card card-body overflow-auto">
                                    <p>
                                        I cookie di analytics ci aiutano a raccogliere informazioni su come utilizzi il
                                        nostro sito, permettendoci di migliorare i nostri servizi e l’esperienza
                                        utente.<br>
                                        Questi cookie <span class="fw-bold">sono opzionali</span> e possono essere
                                        disattivati, ma ci forniscono preziose informazioni per rendere la navigazione
                                        più efficiente.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane col-12  row d-flex " id="nav-cookies" role="tabpanel"
                        aria-labelledby="nav-cookies-tab">

                        <div id="cookie-toggler-container" class="col-md-6 col-12">
                            <!-- caricati con ajax-->
                        </div>
                        <div id="cookie-description-collapsable-container" class="col-md-6 col-12">
                            <!-- caricati con ajax-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex align-items-center col-12">
                <!-- Pulsanti per schermi grandi -->
                <div class="d-none d-sm-flex justify-content-between w-100">
                    <button type="button" class="btn btn-outline-purple accept-cookies col-auto"
                        id="accept-personalized-cookie-large">
                        Personalizzati
                    </button>
                    <button type="button" class="btn btn-purple accept-cookies col-auto" id="accept-all-cookie-large">
                        Accetta Tutti
                    </button>
                    <button type="button" class="btn btn-outline-danger col-auto" id="deny-all-cookie-large">
                        Rifiuta Tutti
                    </button>
                </div>

                <!-- Icone per schermi piccoli -->
                <div class="d-sm-none d-flex justify-content-between w-100">
                    <button type="button" class="btn btn-outline-purple" id="accept-personalized-cookie-icon"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Personalizzati">
                        <i class="fa-solid fa-sliders"></i>
                    </button>
                    <button type="button" class="btn btn-purple" id="accept-all-cookie-icon" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Accetta Tutti">
                        <i class="fa-solid fa-check"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="deny-all-cookie-icon"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Rifiuta Tutti">
                        <i class="fa-solid fa-ban"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>