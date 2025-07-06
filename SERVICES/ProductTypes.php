<?php

trait ProductTypes {
    /**
     * SERVIZIO che restituisce tutti livelli di corsi disponibili con il relativo livello di difficoltà.
     *
     * @return JSONResult
     *   - [OK, LEVELS, $levels] dove $levels è un array di tuple.
     */
    public function getAllCourseLevels(): JSONResult
    {
        $levels = $this->DB->executeQuery("get_all_course_levels", [], true);
        return JSONResult::createOK("LEVELS", $levels);
    }

    /**
     * SERVIZIO che restituisce tutti tipi di corsi disponibili, con relativa descrizione e icona
     * 
     * Eclude il tipo "Eventi".
     *
     * @return JSONResult
     *   - [OK, COURSES, $courses] dove $courses è un array di tuple.
     */
    public function getAllCourseTypes(): JSONResult
    {
        $courses = $this->DB->executeQuery("get_all_course_types", [], true);
        return JSONResult::createOK("COURSES", $courses);
    }

    /**
     * SERVIZIO che restituisce tutte le discipline disponibili (nome)
     * 
     * @return JSONResult
     *   - [OK, DISCIPLINES, $disciplines] dove $disciplines è un array di tuple.
     */
    public function getAllDanceDisciplines(): JSONResult
    {
        $disciplines = $this->DB->executeQuery("get_all_dance_disciplines", [], true);
        return JSONResult::createOK("DISCIPLINES", $disciplines);
    }
}