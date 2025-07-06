<?php
//definisco i link e i messaggi personalizzati per ogni metodo di contatto

/***WHATSAPP***/
//rimuovo tutti i caratteri che non sono numerici
$phone_number = "393338689245";

$message = 'Ciao, vorrei avere maggiori informazioni riguardo ai corsi che proponete';

// URL encode del messaggio 
$encoded_message = urlencode($message);

// Crea l'URL per WhatsApp
$whatsapp_url = "https://wa.me/{$phone_number}?text={$encoded_message}";

/***EMAIL***/
$to = "fbslatinempire@gmail.com";
$encoded_subject = rawurlencode("Richiesta di Informazioni sui Corsi di Ballo");
$encoded_body = rawurlencode(
  "Gentile Team,
  Sono interessato ai vostri corsi di ballo e vorrei ricevere maggiori informazioni.
  In particolare, mi piacerebbe sapere:
  - I tipi di corsi disponibili
  - I prezzi
  - Gli orari
  - Le date di inizio
  Vi ringrazio anticipatamente per la vostra disponibilità.
  Grazie e cordiali saluti"
);
$email_url = "mailto:{$to}?subject={$encoded_subject}&body={$encoded_body}";

/***TIKTOK***/
$tiktok_url = "https://www.tiktok.com/@fbslatinempire";

/***FACEBOOK***/
$facebook_url = "https://www.facebook.com/fbslatinempire/";

/***INSTAGRAM***/
$instagram_url = "https://www.instagram.com/fbslatinempire";

/***APPLE MUSIC***/
$apple_music_url = ERROR_PAGE; //zio pera non trova il profilo (non esiste?)

?>

<section id="contatti" class="contatti section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Contattaci</h2>
    <div class="d-flex align-items-center justify-content-between" data-aos="fade-up" data-aos-delay="300">
      <p class="mb-0">Vieni a trovarci</p>
      <a href="https://maps.app.goo.gl/ET3gVREZ4ERkRjoPA" target="_blank"
        class="info-item d-flex align-items-center d-none d-md-flex">
        <i class="bi bi-geo-alt flex-shrink-0"></i>
        <div class="ms-2">
          <h3>Indirizzo</h3>
          <p>Corso Italia 3, 16145 Genova GE</p>
        </div>
      </a>
    </div>

    <!-- Link sotto "Vieni a trovarci" solo per schermi piccoli -->
    <a href="https://maps.app.goo.gl/ET3gVREZ4ERkRjoPA" target="_blank"
      class="d-block d-md-none mt-2 text-decoration-underline">
      Apri in Google Maps
    </a>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div id="map" class="mb-2" data-aos="fade-up" data-aos-delay="200">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5701.949495141003!2d8.951476!3d44.392640099999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12d343b1458b2579%3A0x7106b8e4278b0458!2sCaribe%20club!5e0!3m2!1sit!2sit!4v1721497878366!5m2!1sit!2sit"
        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div><!-- End Google Maps -->

    <div class="row">

      <!-- WHATSAPP -->
      <div class="col-md-6 info-item d-flex mb-2" data-aos="fade-up" data-aos-delay="300">
        <a href="<?= $whatsapp_url ?>" target="_blank" class="d-flex align-items-center">
          <i class="bi bi-whatsapp"></i>
          <div>
            <h3>WhatsApp</h3>
            <p>+39 333 8689245</p>
          </div>
        </a>
      </div>

      <!-- EMAIL -->
      <div class="col-md-6 info-item d-flex mb-2" data-aos="fade-up" data-aos-delay="300">
        <a href="<?= $email_url ?>" target="_blank" class="d-flex align-items-center">
          <i class="bi bi-envelope"></i>
          <div>
            <h3>E-mail</h3>
            <p>fbslatinempire@gmail.com</p>
          </div>
        </a>
      </div>

      <!-- TIKTOK -->
      <div class="col-md-6 info-item d-flex mb-2" data-aos="fade-up" data-aos-delay="300">
        <a href="<?= $tiktok_url ?>" target="_blank" class="d-flex align-items-center">
          <i class="bi bi-tiktok"></i>
          <div>
            <h3>TikTok</h3>
            <p>@fbslatinempire</p>
          </div>
        </a>
      </div>

      <!-- FACEBOOK -->
      <div class="col-md-6 info-item d-flex mb-2" data-aos="fade-up" data-aos-delay="300">
        <a href="<?= $facebook_url ?>" target="_blank" class="d-flex align-items-center">
          <i class="bi bi-facebook"></i>
          <div>
            <h3>Facebook</h3>
            <p>FBS Latin Empire</p>
          </div>
        </a>
      </div>

      <!-- INSTAGRAM -->
      <div class="col-md-6 info-item d-flex mb-2" data-aos="fade-up" data-aos-delay="300">
        <a href="<?= $instagram_url ?>" target="_blank" class="d-flex align-items-center">
          <i class="bi bi-instagram"></i>
          <div>
            <h3>Instagram</h3>
            <p>@fbslatinempire</p>
          </div>
        </a>
      </div>

      <!-- APPLE MUSIC -->
      <div class="col-md-6 info-item d-flex mb-2" data-aos="fade-up" data-aos-delay="300">
        <a href="<?= $apple_music_url ?>" target="_blank" class="d-flex align-items-center">
          <i class="bi bi-music-note-beamed"></i>
          <div>
            <h3>AppleMusic</h3>
            <p>@fbslatinempire</p>
          </div>
        </a>

      </div>
    </div>
  </div>
</section>