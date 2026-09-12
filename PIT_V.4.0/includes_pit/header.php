<?php
// 🔥 RUTA BASE DINÁMICA
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SIST V.4.0/PIT_V.4.0/";
?>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

/* ==========================
   HEADER PIT
========================== */

.header-pit{
    background:#e6e6e6;
    padding:10px 20px;

    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;

    flex-wrap:wrap;
}

/* IZQUIERDA */

.header-left{
    display:flex;
    align-items:center;
    gap:15px;
}

.header-left img{
    height:60px;
    width:auto;
}

/* CENTRO */

.header-center{
    flex:1;
    text-align:center;
}

.header-center h1{
    color:#8B1C1C;
    font-family:Arial, Helvetica, sans-serif;
    font-size:36px;
    font-weight:bold;
}

.header-center p{
    margin-top:4px;
    font-size:16px;
    color:#333;
    font-weight:bold;
    letter-spacing:1px;
}

/* DERECHA */

.header-right{
    display:flex;
    align-items:center;
    gap:30px;
}

.header-right img{
    height:50px;
    width:auto;
}

/* ==========================
   TABLETS
========================== */

@media (max-width:992px){

    .header-left img{
        height:50px;
    }

    .header-right img{
        height:42px;
    }

    .header-center h1{
        font-size:30px;
    }

    .header-center p{
        font-size:14px;
    }

}

/* ==========================
   CELULARES
========================== */

@media (max-width:768px){

    .header-pit{
        flex-direction:column;
        text-align:center;
        gap:15px;
        padding:15px;
    }

    .header-center{
        order:-1;
    }

    .header-left,
    .header-right{
        justify-content:center;
        flex-wrap:wrap;
        gap:15px;
    }

    .header-left img{
        height:45px;
    }

    .header-right img{
        height:38px;
    }

    .header-center h1{
        font-size:28px;
    }

    .header-center p{
        font-size:13px;
        letter-spacing:.5px;
    }

}

/* ==========================
   CELULARES PEQUEÑOS
========================== */

@media (max-width:480px){

    .header-pit{
        padding:10px;
    }

    .header-left{
        gap:10px;
    }

    .header-right{
        gap:10px;
    }

    .header-left img{
        height:38px;
    }

    .header-right img{
        height:30px;
    }

    .header-center h1{
        font-size:22px;
    }

    .header-center p{
        font-size:11px;
        line-height:1.3;
    }

}

</style>

<body>

<div class="app-layout">

<header class="header-pit">

    <!-- LOGOS IZQUIERDA -->
    <div class="header-left">
        <img src="<?php echo $base_url; ?>assets_pit/620618e0cdd94000043e3950.png" alt="">
        <img src="<?php echo $base_url; ?>assets_pit/logo-header.png" alt="">
    </div>

    <!-- CENTRO -->
    <div class="header-center">
        <h1>PIT</h1>
        <p>Programa Institucional de Tutorías</p>
    </div>

    <!-- LOGOS DERECHA -->
    <div class="header-right">
        <img src="<?php echo $base_url; ?>assets_pit/image.png" alt="">
        <img src="<?php echo $base_url; ?>assets_pit/tescha.png" alt="">
        <img src="<?php echo $base_url; ?>assets_pit/anuies.png" alt="">
    </div>

</header>

</div>

</body>