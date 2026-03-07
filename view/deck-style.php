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
        position: relative;
        width: 100%;
        box-sizing: border-box;
        background: linear-gradient(180deg, #151a25 0%, #0a0c10 100%);
        border: 2px solid #2a3143;
        border-radius: 16px;
        padding: 40px 20px 20px;
        margin-bottom: 50px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
    }
    .wrap-deck h2 {
        text-transform: uppercase;
        position: absolute;
        top: 0;
        left: 50%;
        transform: translate(-50%, -50%);
        margin: 0;
        padding: 5px 20px;
        background: #151a25;
        border: 2px solid #2a3143;
        border-radius: 20px;
        color: #8ab4f8;
        font-size: 18px;
        letter-spacing: 1.5px;
        white-space: nowrap;
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
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
            padding: 60px 40px 40px 40px;
            border: 2px solid #3a4563;
            border-radius: 24px;
            background: linear-gradient(180deg, #131720 0%, #0a0c10 100%);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }
        .wrap-deck h2 {
            top: 0;
            left: 40px;
            transform: translateY(-50%);
            padding: 8px 30px;
            background: #131720;
            border: 2px solid #3a4563;
            border-radius: 12px;
            font-size: 22px;
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
