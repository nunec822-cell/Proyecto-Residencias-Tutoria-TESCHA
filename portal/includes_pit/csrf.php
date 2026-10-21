<?php
/*=========================================================
    PROTECCIÓN CSRF EN EL NAVEGADOR (se incluye desde cada sidebar y header.php)

    Agrega solo el token de la sesión a todo lo que se envía por POST desde la página:
      - formularios normales (campo oculto csrf_token, también en formularios que
        se cargan después, como los modales)
      - peticiones fetch() y XMLHttpRequest/jQuery (cabecera X-CSRF-Token)
    El servidor lo comprueba con sist_requerir_csrf() / sist_requerir_csrf_post().
    Así los formularios existentes no necesitan cambios.
=========================================================*/

if (defined('SIST_CSRF_JS_INCLUIDO')) {
    return;
}
define('SIST_CSRF_JS_INCLUIDO', true);

require_once __DIR__ . '/../../core/bootstrap.php';
?>
<meta name="csrf-token" content="<?= e(sist_csrf_token()) ?>">
<script>
(function () {
    if (window.__sistCsrf) return;
    var token = <?= json_encode(sist_csrf_token()) ?>;
    window.__sistCsrf = token;

    function mismoOrigen(url) {
        try { return new URL(url, location.href).origin === location.origin; } catch (e) { return false; }
    }

    /* Formularios: campo oculto (al cargar, al agregarse y justo antes de enviarse) */
    function ponerCampo(form) {
        if (!form || (form.method || '').toLowerCase() !== 'post') return;
        if (form.querySelector('input[name="csrf_token"]')) return;
        var i = document.createElement('input');
        i.type = 'hidden'; i.name = 'csrf_token'; i.value = token;
        form.appendChild(i);
    }
    function revisarTodo(raiz) {
        if (raiz.tagName === 'FORM') ponerCampo(raiz);
        if (raiz.querySelectorAll) raiz.querySelectorAll('form').forEach(ponerCampo);
    }
    document.addEventListener('submit', function (ev) { ponerCampo(ev.target); }, true);
    document.addEventListener('DOMContentLoaded', function () {
        revisarTodo(document);
        new MutationObserver(function (cambios) {
            cambios.forEach(function (c) {
                c.addedNodes.forEach(function (n) { if (n.nodeType === 1) revisarTodo(n); });
            });
        }).observe(document.body, { childList: true, subtree: true });
    });

    /* fetch(): cabecera en peticiones al mismo sitio */
    if (window.fetch) {
        var fetchOriginal = window.fetch;
        window.fetch = function (recurso, opciones) {
            var url = (recurso && recurso.url) || recurso;
            if (mismoOrigen(url)) {
                opciones = opciones || {};
                var h = new Headers(opciones.headers || (recurso && recurso.headers) || {});
                if (!h.has('X-CSRF-Token')) h.set('X-CSRF-Token', token);
                opciones.headers = h;
            }
            return fetchOriginal.call(this, recurso, opciones);
        };
    }

    /* XMLHttpRequest (y jQuery.ajax): cabecera en peticiones al mismo sitio */
    var abrir = XMLHttpRequest.prototype.open, enviar = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.open = function (metodo, url) {
        this.__sistMismo = mismoOrigen(url);
        return abrir.apply(this, arguments);
    };
    XMLHttpRequest.prototype.send = function () {
        if (this.__sistMismo) { try { this.setRequestHeader('X-CSRF-Token', token); } catch (e) {} }
        return enviar.apply(this, arguments);
    };
})();
</script>
