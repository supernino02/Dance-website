<?php

/**
 * Classe che gestisce l'inclusione di componenti PHP, CSS e JS.
 * - Imposta le directory per i file PHP, CSS e JS.
 * - Tiene traccia delle componenti già incluse per evitare duplicazioni.
 * - Permette di includere dinamicamente file PHP di componenti specifiche.
 * - Include, se esistono, i relativi file CSS e JS per ogni componente.
 */

final Class ComponentManager {
    static ?string $components_directory = null;
    static ?string $css_directory = null;
    static ?string $js_directory = null;

    static ?array $components_included = null;
    
    // inizializzo le componenti incluse con dentro il nome della pagina chiamata.
    static function initialize()
    {
        self::$components_included = [basename($_SERVER['PHP_SELF'], '.php')];
    }

    // imposta la directory delle componenti php
    static function setcomponentsDirectory(string $path) {
        self::$components_directory = $path;
    }

    // imposta la directory dei file css
    static function setCssDirectory(string $path)
    {
        self::$css_directory = $path;
    }

    // imposta la directory dei file js
    static function setJsDirectory(string $path)
    {
        self::$js_directory = $path;
    }

    // aggiunge una componente alla lista delle componenti incluse
    static function includeComponent(string $component)
    {
        //se non ancora presente nell'array, lo aggiungo
        if (!in_array($component, self::$components_included))
            self::$components_included[] = $component; 

        //includo la componente richiesta
        include self::$components_directory . $component . ".php";
    }

    // includo tutti i file css delle componenti incluse
    static function includeAllCssFiles() {
        return; // ! FUNZIONE INUTILIZZATA, poiché tutti i file CSS (pochi) sono inclusi nella componente head.php

        echo "<!-- SPECIFIC CSS File -->" . PHP_EOL;
        //per ogni componente inclusa, se possibile aggiungo il file css
        foreach (self::$components_included as $component) {
            $path_css = self::$css_directory . $component . ".css";
            if (file_exists($path_css))
                echo "<link href='{$path_css}' rel='stylesheet'>" . PHP_EOL; 
        }
        echo PHP_EOL;
    }

    // includo tutti i file js delle componenti incluse
    static function includeAllJsFiles()
    {
        //includo i file generici per il js
        echo "
<!-- CUSTOM JS File -->
<script src=JS/main.js></script>
<script src=JS/functions.js></script>" . PHP_EOL . PHP_EOL;

        echo "<!-- SPECIFIC JS File -->" . PHP_EOL;
        //per ogni componente inclusa, se possibile aggiungo il file js
        foreach (self::$components_included as $component) {
            $path_js = self::$js_directory . $component . ".js";
            if (file_exists($path_js))
                echo "<script src='{$path_js}'></script>" . PHP_EOL;
        }
        echo PHP_EOL;
    }


}