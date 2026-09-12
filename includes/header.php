<?php
// 🔥 RUTA BASE DINÁMICA
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SIST V.4.0/";
?>

<style>

/* RESET */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

/* ==========================
   HEADER
========================== */

.header-sist{
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
    margin:0;
}

.header-center p{
    margin-top:5px;
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

.header-right img:last-child{
    height:65px;
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

    .header-right img:last-child{
        height:55px;
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

    .header-sist{
        flex-direction:column;
        justify-content:center;
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

    .header-right img:last-child{
        height:48px;
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

    .header-sist{
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

    .header-right img:last-child{
        height:38px;
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

<header class="header-sist">

    <!-- LOGOS IZQUIERDA -->
    <div class="header-left">
        <img src="<?php echo $base_url; ?>assets/620618e0cdd94000043e3950.png" alt="Logo 1">
        <img src="<?php echo $base_url; ?>assets/logo-header.png" alt="Logo 2">
    </div>

    <!-- CENTRO -->
    <div class="header-center">
        <h1>SIST</h1>
        <p>Sistema Integral de Seguimiento Tutorial</p>
    </div>

    <!-- LOGOS DERECHA -->
    <div class="header-right">
        <img src="<?php echo $base_url; ?>assets/image.png" alt="Logo 3">
        <img src="<?php echo $base_url; ?>assets/tescha.png" alt="TESCHA">
        <img src="<?php echo $base_url; ?>assets/anuies.png" alt="ANUIES">
    </div>

</header>

</div>

</body>

