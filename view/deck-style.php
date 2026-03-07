<style>
    <?php if (URL === 'deck' || isset($_GET['id']) || isset($_GET['create'])) { ?>
    .wrap {
        width: 900px;
    }
    <?php } ?>
    h2 {
        text-align: center;
        margin:20px auto 30px auto;
        font-size: 31px;
        border:none;
        padding:0;
    }
    .saved {
        display: inline;
        margin-left:38px;
    }
    .saved div {
        display: inline-block;
        height:191px;
        width: 107px;
    }
    .saved div img {
        width:100px;
        position:absolute;
    }
    .saved .extra-count {
        position:absolute;
        margin-left:30px;
        margin-top:95px;
        font-weight:bold;
        background-color:black;
        padding:6px;
        height:23px;
        width:30px;
    }
    .saved div img:hover {
        transform:scale(2.5);
        transition: all 0.2s ease;
        z-index: 99999;
    }
    .wrap-deck {
        width:100%;
        background-color:#0a0a0a;
    }
    .wrap-deck h2 {
        text-transform: uppercase;
    }
    .deck-list {
        font-family:monospace;
        font-size:18px;
        padding:0 0 50px 10px;
    }
    .deck-list a {
        text-decoration:none;
        color: #ddd;
    }
    .deck-list a:hover {
         color: #68a;
         text-decoration:underline;
    }
    .frames-data {
        font-size:10px;
        display: table !important;
        padding: 0 !important;
        margin: 0 auto;
        line-height: 17px;
    }

    @media (min-width: 1000px) { /* Desktop */
        .wrap-deck {
            width:100%;
            background-color:#0a0a0a;
            padding:80px 35px 35px 35px;
            border:4px solid #555;
            border-radius:45px;
            margin-bottom:50px;
        }
        .wrap-deck h2 {
            position: absolute;
            margin: -118px 0 0 30px;
            padding: 14px;
            background-color: #0a0a0a;
            border: 4px solid #555;
            border-bottom: none;
        }
        .deck-list {
            margin:10px 40px 10px 40px;
            display: flex;
            width:80%;
            padding:0 0 0 50px;
        }
        .deck-list div {
            flex: 50%;
        }
    }
</style>
