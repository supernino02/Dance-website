<section id="corsi" class="corsi section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
            <div class="col-12">
                <h3 class="text-center"><b>I NOSTRI CORSI</b></h3>
                <p class="fst-italic text-center">
                    Impara a ballare nel modo che preferisci, in un ambiente accogliente e divertente.
                </p>
            </div>

            <div class="col-lg-6 order-1 order-lg-2">
                <img src="MULTIMEDIA/imgs/4images.png" class="img-fluid img-shadow" alt="immagine collage dei corsi collettivi">
            </div>

            <div class="col-lg-6 order-2 order-lg-1">
                <ul id="coursesDescriptions">
                    <?php
                    global $course_types;
                    // Chiamata al servizio che restituisce i tipi di corsi già fatta in header
                    // Itera attraverso l'array per estrarre i campi "description"
                    foreach ($course_types as $course) {
                        $description = $course['description'];
                        echo "<li><p>{$description}</p></li>" . PHP_EOL;
                    }
                    ?>
                </ul>
            </div>
        </div>
        <br>
        <br>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <!-- bottoni di corsi, popolata da ajax -->
        <div id="coursesButtons" class="corsi-buttons">
            <?php // Chiamata al servizio che restituisce i tipi di corsi già fatta in header
            foreach ($course_types as $course) {
                $type = $course['type'];

                echo "
                <a class='corsi-button' href='courses.php' onclick='resetFilters(\"Type\",\"{$type}\")'>
                    <img class='img-fluid' src='{$course['icon_path']}' alt='icona per corsi di tipo {$type}'>
                    <b>{$type}</b>
                </a>";
            }
            ?>
        </div>

        <br>
        <!-- Avviso che le lezioni online possono essere acquistate sul sito ma per le lezioni private, corsi collettivi e corsi coreografici, sarà necessario contattarci -->
        <div class="row gy-4">
            <p class="fst-italic text-center">
                Acquista le nostre lezioni online qui sotto. Per le lezioni private, corsi
                collettivi e corsi coreografici, si consiglia prima di contattarci.
            </p>
        </div>

    </div>
</section>