<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Questa interfaccia rappresenta il contratto che le classi che implementano
 * il filtro devono avere. In questo caso ho utilizzato un'interfaccia perchè
 * si tratta solo di un metodo, ma nulla vieta di utilizzare una classe abstract
 * derivante da "GGC_Provider".
 * 
 * @author Gianni Carafone
 */
interface GGC_IFilter {
    function doFilter(GGC_HttpRequest $request, GGC_HttpResponse $response,
                GGC_FilterChain $filterChain);
}

?>
