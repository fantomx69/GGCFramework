<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_Filter
 * 
 * Questa classe offre le unzionalità di base per implementare un fltro di
 * per i dati di request e response. Ovviamente, per ogni funzionalità particlare
 * che il filtro deve svolgere, si creerà una classe che eredita da questa e
 * implementi le funznalità opportune.
 *
 * @author Gianni Carafone
 */

class GGC_Filter implements GGC_IFilter {
    /*
     * Richiamato dalle classi derivate prima della loro impementazione.
     */
    function doFilter(\GGC_HttpRequest $request, \GGC_HttpResponse $response, \GGC_FilterChain $filterChain) {
        // Impostazione filtro corrente e flag di fine filtri
    }
}

?>
