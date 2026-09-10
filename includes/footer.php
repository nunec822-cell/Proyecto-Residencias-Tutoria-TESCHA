<?php
// Genera mes y año automáticamente (ej: Abril 2026)
setlocale(LC_TIME, 'es_ES.UTF-8');
$month_year = strftime("%B %Y");
?>

<footer style="background-color: #333; color: white; text-align: center; padding: 8px 15px; font-size: 0.75em;">
    
    <p style="margin: 5px 0;">
        &copy; <?php echo ucfirst($month_year); ?> Tecnológico de Estudios Superiores de Chalco (TESCHA) | Todos los derechos reservados
    </p>

    <div style="margin-bottom: 5px;">
        <p style="margin: 3px 0;">Contacto:</p>
        <p style="margin: 3px 0;">Carretera Federal México Cuautla s/n, Chalco, Edo. de México</p>

        <p style="margin: 3px 0;">
            Facebook: 
            <a href="https://www.facebook.com/TESCHAOficial" target="_blank" style="color: #fff; text-decoration: none;">
                TESCHA
            </a>
        </p>

        <p style="margin: 3px 0;">
            Teléfonos: (1112) 5572955468 y 5559821089
        </p>

        <p style="margin: 3px 0;">
            Correo electrónico: 
            <a href="mailto:dir.general@tesch.edu.mx" style="color: #fff; text-decoration: none;">
                depto.academico.desarrollo@tesch.edu.mx
            </a>
        </p>
    </div>

    <p style="margin: 3px 0;">
        <a href="../privacidad.php" style="color: #fff; text-decoration: none;">
            Política de privacidad
        </a> 
        | 
        <a href="../terminos.php" style="color: #fff; text-decoration: none;">
            Términos y condiciones
        </a>
    </p>

    <p style="font-size: 0.7em; margin-top: 15px; color: #bbb; font-weight: bold; text-transform: uppercase;">
        <a href="/SIST%20V.4.0/datos_footer/equipo_desarrolladores/index.php" style="color: #fff; text-decoration: none;">
            Conoce al equipo de desarrollo
        </a>
    </p>
</div> <!-- cierre de app-layout -->
</body>
</footer>

